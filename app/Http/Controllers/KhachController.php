<?php

namespace App\Http\Controllers;

use App\Models\Khach;
use Illuminate\Http\Request;

class KhachController extends Controller
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
        //
        $validatedData = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'cmnd' => 'required|string|max:20|unique:khach,cmnd',
            'sdt' => 'required|string|max:15|unique:khach,sdt',
            'email' => 'nullable|email|max:255|unique:khach,email',
            'dia_chi' => 'nullable|string|max:255',
        ]);
        $khach = Khach::create($validatedData);
        return response()->json(
            [
                'message' => 'Khách hàng đã được tạo thành công',
                'khach' => $khach,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Khach $khach)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Khach $khach)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Khach $khach)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Khach $khach)
    {
        //
    }
}
