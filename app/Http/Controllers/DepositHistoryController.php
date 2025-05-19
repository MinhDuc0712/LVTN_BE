<?php

namespace App\Http\Controllers;

use App\Models\Deposit_history;
use Illuminate\Http\Request;

class DepositHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $deposit_history = Deposit_history::all();
        return response()->json($deposit_history);
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
        $data = $request->validate([
            'so_tien' => 'required|numeric',
            'khuyen_mai' => 'nullable|numeric',
            'thuc_nhan' => 'required|numeric',
            'phuong_thuc' => 'required|string',
            'ma_giao_dich' => 'required|string|unique:deposit_history,ma_giao_dich',
            'trang_thai' => 'required|string',
            'ghi_chu' => 'required|string',
        ]);

        $data['ngay_nap'] = now();

        $deposit_history = Deposit_history::create($data);

        return response()->json(['message' => 'Lưu thành công', 'data' => $deposit_history],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Deposit_history $deposit_history)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deposit_history $deposit_history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposit_history $deposit_history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit_history $deposit_history)
    {
        //
    }
}
