<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Group;
use App\Models\Campaign;
use App\Models\Message;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
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
                ->get()
        ];

        return view('dashboard.index', $data);
    }
}
