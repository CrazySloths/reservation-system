<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    /**
     * Display list of saved payment methods
     */
    public function index()
    {
        $userId = session('user_id');

        $paymentMethods = DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('citizen.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Show form to add new payment method
     */
    public function create()
    {
        return view('citizen.payment-methods.create');
    }

    /**
     * Store new payment method
     */
    public function store(Request $request)
    {
        $userId = session('user_id');

        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:gcash,paymaya,bank_transfer',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // If setting as default, remove default from other methods
        if ($request->is_default) {
            DB::connection('auth_db')->table('citizen_payment_methods')
                ->where('user_id', $userId)
                ->update(['is_default' => false]);
        }

        DB::connection('auth_db')->table('citizen_payment_methods')->insert([
            'user_id' => $userId,
            'payment_type' => $request->payment_type,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'is_default' => $request->is_default ?? false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('citizen.payment-methods.index')
            ->with('success', 'Payment method added successfully!');
    }

    /**
     * Show form to edit payment method
     */
    public function edit($id)
    {
        $userId = session('user_id');

        $paymentMethod = DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$paymentMethod) {
            return redirect()->route('citizen.payment-methods.index')
                ->with('error', 'Payment method not found.');
        }

        return view('citizen.payment-methods.edit', compact('paymentMethod'));
    }

    /**
     * Update payment method
     */
    public function update(Request $request, $id)
    {
        $userId = session('user_id');

        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:gcash,paymaya,bank_transfer',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify ownership
        $exists = DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            return redirect()->route('citizen.payment-methods.index')
                ->with('error', 'Payment method not found.');
        }

        // If setting as default, remove default from other methods
        if ($request->is_default) {
            DB::connection('auth_db')->table('citizen_payment_methods')
                ->where('user_id', $userId)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('id', $id)
            ->update([
                'payment_type' => $request->payment_type,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'is_default' => $request->is_default ?? false,
                'updated_at' => now()
            ]);

        return redirect()->route('citizen.payment-methods.index')
            ->with('success', 'Payment method updated successfully!');
    }

    /**
     * Delete payment method
     */
    public function destroy($id)
    {
        $userId = session('user_id');

        $deleted = DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        if ($deleted) {
            return redirect()->route('citizen.payment-methods.index')
                ->with('success', 'Payment method deleted successfully!');
        }

        return redirect()->route('citizen.payment-methods.index')
            ->with('error', 'Payment method not found.');
    }

    /**
     * Set payment method as default
     */
    public function setDefault($id)
    {
        $userId = session('user_id');

        // Verify ownership
        $exists = DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Payment method not found.'], 404);
        }

        // Remove default from all methods
        DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('user_id', $userId)
            ->update(['is_default' => false]);

        // Set this as default
        DB::connection('auth_db')->table('citizen_payment_methods')
            ->where('id', $id)
            ->update(['is_default' => true]);

        return response()->json(['success' => true, 'message' => 'Default payment method updated!']);
    }
}

