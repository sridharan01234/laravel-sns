<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Imports\CustomersImport;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('group')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when($request->group_id, function ($query, $groupId) {
                $query->where('group_id', $groupId);
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            });

        $customers = $query->latest()->paginate(10);
        $groups = Group::all();

        return view('customers.index', compact('customers', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:customers,mobile',
            'email' => 'nullable|email|unique:customers,email',
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'required|boolean'
        ]);

        $customer = Customer::create($validated);

        return response()->json($customer);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:customers,mobile,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'required|boolean'
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'group_id' => 'nullable|exists:groups,id'
        ]);

        Excel::import(new CustomersImport($request->group_id), $request->file('file'));

        return response()->json(['message' => 'Customers imported successfully']);
    }

    public function export()
    {
        return Excel::download(new CustomersExport, 'customers.csv');
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

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'nullable|boolean'
        ]);

        $data = array_filter($request->only(['group_id', 'status']));
        
        Customer::whereIn('id', $request->ids)->update($data);

        return response()->json(['message' => 'Customers updated successfully']);
    }

    public function apiIndex(Request $request)
{
    $customers = Customer::with('group')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->paginate(10);

    return response()->json($customers);
}

}
