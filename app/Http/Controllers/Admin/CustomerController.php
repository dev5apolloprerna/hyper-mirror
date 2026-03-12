<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('strCustomer', 'like', "%{$search}%")
                  ->orWhere('strMobile', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('iCustomerId', 'desc')->paginate(10)->withQueryString();

        return view('admin.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customer.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'strCustomer' => 'required|string|max:100',
            'strMobile'   => 'required|digits:10|unique:customers,strMobile',
            'strAddress'  => 'nullable|string',
        ]);

        Customer::create([
            'strCustomer' => $request->strCustomer,
            'strMobile'   => $request->strMobile,
            'strAddress'  => $request->strAddress,
        ]);

        return redirect()->route('admin.customer.index')->with('success', 'Customer added successfully.');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('admin.customer.form', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'strCustomer' => 'required|string|max:100',
            'strMobile'   => [
                'required',
                'digits:10',
                Rule::unique('customers', 'strMobile')->ignore($customer->iCustomerId, 'iCustomerId')
            ],
            'strAddress'  => 'nullable|string',
        ]);

        $customer->update([
            'strCustomer' => $request->strCustomer,
            'strMobile'   => $request->strMobile,
            'strAddress'  => $request->strAddress,
        ]);

        return redirect()->route('admin.customer.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customer.index')->with('success', 'Customer deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:customers,iCustomerId',
        ]);

        Customer::whereIn('iCustomerId', $request->ids)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Selected customers deleted successfully.'
        ]);
    }
}
