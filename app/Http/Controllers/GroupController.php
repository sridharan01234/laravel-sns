<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Customer;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('customers')->get();
        return view('groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $group = Group::create($validated);

        return response()->json($group);
    }

    public function show(Group $group)
    {
        return response()->json($group);
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $group->update($validated);

        return response()->json($group);
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json(['message' => 'Group deleted successfully']);
    }

    public function addCustomers(Request $request, Group $group)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id'
        ]);

        Customer::whereIn('id', $request->customer_ids)
            ->update(['group_id' => $group->id]);

        return response()->json(['message' => 'Customers added successfully']);
    }

    public function customers(Group $group)
    {
        $customers = $group->customers()->get();
        return response()->json($customers);
    }

    public function removeCustomer(Group $group, Customer $customer)
    {
        if ($customer->group_id === $group->id) {
            $customer->update(['group_id' => null]);
        }

        return response()->json(['message' => 'Customer removed from group']);
    }
}
