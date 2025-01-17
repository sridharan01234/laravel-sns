<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('group')
            ->latest()
            ->paginate(10);

        return CustomerResource::collection($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:customers,mobile',
            'email' => 'nullable|email|unique:customers,email',
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'boolean'
        ]);

        $customer = Customer::create($validated);

        return new CustomerResource($customer);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id'
        ]);

        Customer::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Customers deleted successfully']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        // Import logic here

        return response()->json(['message' => 'Customers imported successfully']);
    }
}
