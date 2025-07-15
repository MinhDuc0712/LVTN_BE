<?php

namespace App\Http\Controllers;

use App\Models\Giadichvu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiadichvuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $prices = Giadichvu::orderBy('ngay_ap_dung', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $prices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load service prices'
            ], 500);
        }
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
    $validator = Validator::make($request->all(), [
        'ten' => 'required|string|max:255',
        'gia_tri' => 'required|numeric|min:0',
        'ngay_ap_dung' => 'required|date'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $price = Giadichvu::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $price,
            'message' => 'Service price created successfully'
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create service price',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
       try {
        $price = Giadichvu::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $price
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy giá dịch vụ'
        ], 404);
    }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(giadichvu $giadichvu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ten' => 'sometimes|string|max:255',
            'gia_tri' => 'sometimes|numeric|min:0',
            'ngay_ap_dung' => 'sometimes|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $price = Giadichvu::findOrFail($id);
            $price->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $price,
                'message' => 'Service price updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service price'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $price = Giadichvu::findOrFail($id);
            $price->delete();
            return response()->json([
                'success' => true,
                'message' => 'Service price deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service price'
            ], 500);
        }
    }
}
