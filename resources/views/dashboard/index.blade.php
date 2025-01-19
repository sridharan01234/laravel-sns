@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">SMS Campaign Dashboard</h1>
    <p class="mt-1 text-sm text-gray-600">Real-time overview of your messaging campaigns and performance</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card transform hover:scale-105 transition-transform duration-200">
        <div class="card-body">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Total Customers</h3>
                    <p class="text-3xl font-bold text-indigo-600 mt-2">{{ number_format($total_customers) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card transform hover:scale-105 transition-transform duration-200">
        <div class="card-body">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Total Groups</h3>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($total_groups) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card transform hover:scale-105 transition-transform duration-200">
        <div class="card-body">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Total Campaigns</h3>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($total_campaigns) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card transform hover:scale-105 transition-transform duration-200">
        <div class="card-body">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Active Campaigns</h3>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($active_campaigns->count()) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-800">Recent Messages</h3>
        </div>
        <div class="card-body">
            <div class="space-y-4">
                @foreach($recent_messages as $message)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $message->recipient_number }}</p>
                        <p class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="badge {{ $message->status === 'sent' ? 'badge-success' : ($message->status === 'failed' ? 'badge-error' : 'badge-warning') }}">
                        {{ ucfirst($message->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-800">Groups Overview</h3>
        </div>
        <div class="card-body">
            <div class="space-y-4">
                @foreach($groups as $group)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                            <span class="text-indigo-600 font-medium">{{ substr($group->name, 0, 1) }}</span>
                        </div>
                        <span class="font-medium text-gray-900">{{ $group->name }}</span>
                    </div>
                    <span class="badge badge-info">
                        {{ number_format($group->customers_count) }} customers
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
                                                
@endsection