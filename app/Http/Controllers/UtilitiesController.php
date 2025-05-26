<?php

namespace App\Http\Controllers;

use App\Models\Utilities;
use Illuminate\Http\Request;

class UtilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $utilities = Utilities::all();
        return response()->json($utilities);
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
        $request->validate([
        'TenTienIch' => 'required|string|max:255|unique:utilities',
    ]);

    $utility = Utilities::create($request->only('TenTienIch'));

    return response()->json([
        'message' => 'Thêm tiện ích thành công',
        'data' => $utility
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Utilities $utilities)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Utilities $utilities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
        'TenTienIch' => 'required|string|max:255|unique:utilities,TenTienIch,'.$id.',MaTienIch',
    ]);

    $utility = Utilities::findOrFail($id);
    $utility->update([
        'TenTienIch' => $request->TenTienIch, 
    ]);

    return response()->json([
        'message' => 'Cập nhật tiện ích thành công',
        'data' => $utility, 
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $utility = Utilities::findOrFail($id);
    
    if ($utility->houses()->exists()) {
        return response()->json([
            'message' => 'Không thể xóa tiện ích vì đang được sử dụng bởi một số nhà',
        ], 422);
    }

    $utility->delete();

    return response()->json([
        'message' => 'Xóa tiện ích thành công',
    ]);
}
}
