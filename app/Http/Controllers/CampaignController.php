<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Group;
use App\Models\Template;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with(['groups', 'template', 'user'])
            ->latest()
            ->paginate(10);

        $templates = Template::where('status', true)->get();

        $groups = Group::all();

        return view('campaigns.index', compact(['campaigns', 'templates', 'groups']));
    }

    public function show(Campaign $campaign)
    {
        try {
            $campaign->load([
                'template',
                'groups.customers',
                'messages.customer',
                'user',
            ]);
    
            // Calculate campaign progress
            $totalMessages = $campaign->messages->count();
            $completedMessages = $campaign->messages->whereIn('status', ['sent', 'failed'])->count();
            
            $campaignData = [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'status' => $campaign->status,
                'progress' => $totalMessages > 0 ? round(($completedMessages / $totalMessages) * 100) : 0,
                'total_messages' => $totalMessages,
                'completed_messages' => $completedMessages,
                'template' => $campaign->template,
                'groups' => $campaign->groups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'customers_count' => $group->customers_count
                    ];
                }),
                'messages' => $campaign->messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'status' => $message->status,
                        'sent_at' => $message->sent_at?->format('Y-m-d H:i:s'),
                        'customer' => [
                            'id' => $message->customer->id,
                            'name' => $message->customer->name,
                            'mobile' => $message->customer->mobile
                        ]
                    ];
                }),
                'user' => [
                    'id' => $campaign->user->id,
                    'name' => $campaign->user->name
                ],
                'created_at' => $campaign->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $campaign->updated_at->format('Y-m-d H:i:s')
            ];
    
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $campaignData
                ]);
            }
    
            return view('campaigns.show', [
                'campaign' => $campaign,
                'campaignData' => $campaignData
            ]);
    
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch campaign details',
                    'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
                ], 500);
            }
    
            return redirect()
                ->route('campaigns.index')
                ->with('error', 'Failed to fetch campaign details');
        }
    }
        
    public function create()
    {
        $templates = Template::where('status', true)->get();
        $groups    = Group::all();

        return view('campaigns.create', compact('templates', 'groups'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'template_id'  => 'required|exists:templates,id',
            'group_ids'    => 'required|array',
            'group_ids.*'  => 'exists:groups,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign = Campaign::create([
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'template_id'  => $validated['template_id'],
            'status'       => $validated['scheduled_at'] ? 'scheduled' : 'draft',
            'scheduled_at' => $validated['scheduled_at'],
            'user_id'      => 1,
        ]);

        $campaign->groups()->attach($validated['group_ids']);

        return json_encode(
            [
                'status'   => 'success',
                'message'  => 'Campaign created successfully',
                'campaign' => $campaign,
            ]
        );
    }

    public function edit(Campaign $campaign)
    {
        $templates = Template::where('status', true)->get();
        $groups    = Group::all();

        return view('campaigns.edit', compact('campaign', 'templates', 'groups'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'template_id'  => 'required|exists:templates,id',
            'group_ids'    => 'required|array',
            'group_ids.*'  => 'exists:groups,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign->update([
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'template_id'  => $validated['template_id'],
            'status'       => $validated['scheduled_at'] ? 'scheduled' : 'draft',
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        $campaign->groups()->sync($validated['group_ids']);

        return redirect()
            ->route('campaigns.index')
            ->with('success', 'Campaign updated successfully');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()
            ->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully');
    }

    public function execute(Campaign $campaign)
    {
        try {
            if (! in_array($campaign->status, ['draft', 'scheduled'])) {
                throw new \Exception('Campaign cannot be executed in its current state.');
            }

            $campaign->update(['status' => 'running']);

            // Dispatch job to handle message sending
            dispatch(new \App\Jobs\ProcessCampaign($campaign));

            return response()->json([
                'message' => 'Campaign execution started successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function duplicate(Campaign $campaign)
    {
        $newCampaign               = $campaign->replicate();
        $newCampaign->name         = $campaign->name . ' (Copy)';
        $newCampaign->status       = 'draft';
        $newCampaign->scheduled_at = null;
        $newCampaign->save();

        // Copy the campaign groups
        $newCampaign->groups()->attach($campaign->groups->pluck('id'));

        return response()->json($newCampaign->load('groups'));
    }

    public function resend(Campaign $campaign)
    {
        $newCampaign               = $campaign->replicate();
        $newCampaign->status       = 'scheduled';
        $newCampaign->scheduled_at = now();
        $newCampaign->save();

        // Copy the campaign groups
        $newCampaign->groups()->attach($campaign->groups->pluck('id'));

        return response()->json(['message' => 'Campaign scheduled for resend']);
    }
}
