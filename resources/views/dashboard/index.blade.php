@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Total Customers</h3>
        <p class="text-3xl">{{ $total_customers }}</p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Total Groups</h3>
        <p class="text-3xl">{{ $total_groups }}</p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Total Campaigns</h3>
        <p class="text-3xl">{{ $total_campaigns }}</p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Active Campaigns</h3>
        <p class="text-3xl">{{ $active_campaigns->count() }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Messages</h3>
        <div class="space-y-4">
            @foreach($recent_messages as $message)
            <div class="border-b pb-2">
                <p class="text-sm text-gray-600">
                    To: {{ $message->recipient_number }}
                </p>
                <p class="text-sm">
                    Status: <span class="font-semibold">{{ $message->status }}</span>
                </p>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Groups Overview</h3>
        <div class="space-y-4">
            @foreach($groups as $group)
            <div class="flex justify-between items-center border-b pb-2">
                <span>{{ $group->name }}</span>
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    {{ $group->customers_count }} customers
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
