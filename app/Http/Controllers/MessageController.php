<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Campaign;
use App\Models\Group;
use App\Models\Customer;
use App\Services\MSG91Service;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private $msg91Service;

    public function __construct(MSG91Service $msg91Service)
    {
        $this->msg91Service = $msg91Service;
    }

    public function dashboard()
    {
        $data = [
            'total_customers' => Customer::count(),
            'total_groups' => Group::count(),
            'total_campaigns' => Campaign::count(),
            'recent_messages' => Message::with(['customer', 'campaign'])
                ->latest()
                ->take(5)
                ->get(),
            'groups' => Group::withCount('customers')->get(),
            'active_campaigns' => Campaign::whereIn('status', ['scheduled', 'running'])
                ->with('template')
                ->get(),
                'campaigns' => Campaign::with('template')->get()
        ];

        return view('dashboard.index', $data);
    }

    public function send(Request $request)
    {
        $request->validate([
            'template_id' => 'required|string',
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id',
            'variables' => 'required|array',
            'schedule_at' => 'nullable|date|after:now'
        ]);

        try {
            $customers = Customer::whereIn('group_id', $request->group_ids)
                ->where('status', true)
                ->get();

            $campaign = Campaign::create([
                'name' => $request->name ?? 'Campaign ' . now()->format('Y-m-d H:i'),
                'template_id' => $request->template_id,
                'status' => $request->schedule_at ? 'scheduled' : 'running',
                'scheduled_at' => $request->schedule_at,
                'user_id' => auth()->id()
            ]);

            $campaign->groups()->attach($request->group_ids);

            if (!$request->schedule_at) {
                foreach ($customers as $customer) {
                    $recipients = [[
                        'mobiles' => $customer->mobile,
                        ...$request->variables,
                        'name' => $customer->name
                    ]];

                    $response = $this->msg91Service->sendMessage(
                        $request->template_id,
                        $recipients
                    );

                    Message::create([
                        'campaign_id' => $campaign->id,
                        'customer_id' => $customer->id,
                        'template_id' => $request->template_id,
                        'recipient_number' => $customer->mobile,
                        'variables' => $request->variables,
                        'status' => $response['type'] ?? 'sent',
                        'response' => $response,
                        'user_id' => auth()->id()
                    ]);
                }

                $campaign->update(['status' => 'completed']);
            }

            return response()->json([
                'message' => $request->schedule_at ? 'Campaign scheduled successfully' : 'Messages sent successfully',
                'campaign' => $campaign->load('groups')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $messages = Message::with(['user', 'customer', 'campaign'])
            ->latest()
            ->paginate(10);

            $campaigns = Campaign::with('template')->get();

        return view('messages.index', compact(['messages', 'campaigns']));
    }
}
