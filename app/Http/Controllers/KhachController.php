<?php

namespace App\Http\Controllers;

use App\Models\Khach;
use Illuminate\Support\Facades\Validator;
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
            'cmnd' => 'required|string|max:20',
            'sdt' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'dia_chi' => 'nullable|string|max:255',
        ]);

        // Check for existing customer by cmnd
        $khach = Khach::where('cmnd', $validatedData['cmnd'])->first();

        if ($khach) {
            return response()->json([
                'message' => 'Khách hàng đã tồn tại',
                'khach' => $khach,
            ], 200);
        }

        // Validate uniqueness for new customer
        $khachValidator = Validator::make($validatedData, [
            'cmnd' => 'unique:khach,cmnd',
            'sdt' => 'unique:khach,sdt',
            'email' => 'nullable|unique:khach,email',
        ]);

        if ($khachValidator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu khách hàng không hợp lệ',
                'errors' => $khachValidator->errors(),
            ], 422);
        }

        $khach = Khach::create($validatedData);

        return response()->json([
            'message' => 'Khách hàng đã được tạo thành công',
            'khach' => $khach,
        ], 201);
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
