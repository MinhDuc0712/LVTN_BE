<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getUserPayments()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Không xác thực được người dùng'], 401);
        }

        $payments = Payments::where('MaNguoiDung', $user->MaNguoiDung)
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payments $payments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payments $payments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payments $payments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payments $payments)
    {
        //
    }
}
