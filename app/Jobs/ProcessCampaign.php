<?php

namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign->load(['template', 'groups.customers']);
    }

    public function handle()
    {
        try {
            // Refresh the campaign to ensure we have the latest data
            $this->campaign->refresh()->load(['template', 'groups.customers']);

            if (!$this->campaign->template) {
                throw new \Exception('Template not found for campaign: ' . $this->campaign->id);
            }

            // Get unique customers from all groups
            $customers = $this->campaign->groups()
                ->with('customers')
                ->get()
                ->pluck('customers')
                ->flatten()
                ->unique('id');

            foreach ($customers as $customer) {
                // Create message with customer_id
                $message = $this->campaign->messages()->create([
                    'customer_id' => $customer->id,
                    'status' => 'pending'
                ]);

                // Dispatch SendMessage job
                SendMessage::dispatch($message);
            }

            // Update campaign status
            $this->campaign->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Campaign Processing Failed: ' . $e->getMessage(), [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->campaign->update(['status' => 'failed']);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Campaign Job Failed: ' . $exception->getMessage(), [
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        $this->campaign->update(['status' => 'failed']);
    }
}
