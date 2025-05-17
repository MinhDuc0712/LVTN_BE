<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
         $data = $request->validate([
        'so_tien' => 'required|numeric',
        'khuyen_mai' => 'nullable|numeric',
        'thuc_nhan' => 'required|numeric',
        'phuong_thuc' => 'required|string|unique:deposit_history,phuong_thuc',
        'ma_giao_dich' => 'required|string|unique:deposit_history,ma_giao_dich',
        'trang_thai' => 'required|string|unique:deposit_history,trang_thai',
        'ghi_chu' => 'required|string|unique:deposit_history,ghi_chu',
    ]);

    $deposit = \App\Models\DepositHistory::create($data);

    return response()->json(['message' => 'Lưu thành công', 'data' => $deposit]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
