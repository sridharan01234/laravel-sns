<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'Recharge Reminder - 10 Days',
                'msg91_template_id' => 'recharge_reminder_10',
                'content' => 'Dear {customer_name}, your {plan_name} plan for {number} will expire in 10 days. Recharge now with Rs.{amount} to continue enjoying uninterrupted services. Visit {recharge_link} or call {support_number} for assistance.',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'number',
                    'amount',
                    'recharge_link',
                    'support_number'
                ],
                'status' => true
            ],
            [
                'name' => 'Recharge Reminder - 5 Days',
                'msg91_template_id' => 'recharge_reminder_5',
                'content' => 'Dear {customer_name}, only 5 days left! Your {plan_name} plan for {number} will expire soon. Recharge with Rs.{amount} to avoid service interruption. Quick recharge: {recharge_link}',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'number',
                    'amount',
                    'recharge_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Recharge Due Tomorrow',
                'msg91_template_id' => 'recharge_due_tomorrow',
                'content' => 'Urgent: Dear {customer_name}, your {plan_name} plan expires tomorrow! Recharge now with Rs.{amount} to avoid service disruption. Quick recharge: {recharge_link}',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'amount',
                    'recharge_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Plan Expired Today',
                'msg91_template_id' => 'plan_expired_today',
                'content' => 'Dear {customer_name}, your {plan_name} plan has expired. Recharge immediately with Rs.{amount} to restore services. Quick recharge: {recharge_link} or call {support_number}',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'amount',
                    'recharge_link',
                    'support_number'
                ],
                'status' => true
            ],
            [
                'name' => 'Special Offer Reminder',
                'msg91_template_id' => 'special_offer_reminder',
                'content' => 'Exclusive offer for {customer_name}! Recharge your {plan_name} plan with Rs.{amount} and get extra {bonus_data}GB data + {bonus_validity} days validity. Offer valid till {offer_expiry}. Recharge now: {recharge_link}',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'amount',
                    'bonus_data',
                    'bonus_validity',
                    'offer_expiry',
                    'recharge_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Auto-Recharge Reminder',
                'msg91_template_id' => 'auto_recharge_reminder',
                'content' => 'Dear {customer_name}, your auto-recharge of Rs.{amount} for {plan_name} plan will be processed tomorrow. Ensure sufficient balance in your linked card. To modify, visit {settings_link}',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'amount',
                    'settings_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Low Balance Alert',
                'msg91_template_id' => 'low_balance_alert',
                'content' => 'Dear {customer_name}, your {plan_name} balance is running low: {remaining_data}GB data & {remaining_days} days left. Recharge now with Rs.{amount} for uninterrupted service: {recharge_link}',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'remaining_data',
                    'remaining_days',
                    'amount',
                    'recharge_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Welcome Message',
                'msg91_template_id' => 'welcome_message',
                'content' => 'Welcome {customer_name}! Your {plan_name} plan is now active. Enjoy {data_limit}GB data with {validity_days} days validity. For support, call {support_number}. Manage your account: {account_link}',
                'variables' => [
                    'customer_name',
                    'plan_name',
                    'data_limit',
                    'validity_days',
                    'support_number',
                    'account_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Plan Upgrade Suggestion',
                'msg91_template_id' => 'plan_upgrade_suggestion',
                'content' => 'Dear {customer_name}, upgrade to our {new_plan_name} plan at just Rs.{new_amount}/month and get {data_limit}GB data + {extra_benefits}. Special discount of {discount}% till {offer_validity}. Upgrade now: {upgrade_link}',
                'variables' => [
                    'customer_name',
                    'new_plan_name',
                    'new_amount',
                    'data_limit',
                    'extra_benefits',
                    'discount',
                    'offer_validity',
                    'upgrade_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Payment Failed',
                'msg91_template_id' => 'payment_failed',
                'content' => 'Dear {customer_name}, your auto-recharge payment of Rs.{amount} for {plan_name} has failed. Please update your payment method or recharge manually to avoid service interruption: {recharge_link}',
                'variables' => [
                    'customer_name',
                    'amount',
                    'plan_name',
                    'recharge_link'
                ],
                'status' => true
            ],
            [
                'name' => 'Testing',
                'msg91_template_id' => 'testing',
                'content' => 'Dear Customer, this is a test message. Please ignore.',
                'status' => true
            ]
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}
