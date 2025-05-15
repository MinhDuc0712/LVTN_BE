<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categories::all();
        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'mo ta' => 'nullable|string|max:1000',
        ]);
        // Kiểm tra xem danh mục đã tồn tại hay chưa
        $existingcategories = Categories::where('name', $request->name)->first();
        if ($existingcategories) {
            return response()->json([
                'message' => 'Danh mục đã tồn tại',
            ], 409);
        }
        // Tạo danh mục mới
        $categories = Categories::create([
            'name' => $request->name,
            'mo ta' => $request->mota,
        ]);

        return response()->json([
            'message' => 'Danh mục đã được tạo thành công',
            'data' => $categories
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categories $categories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categories $categories)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categories $categories)
    {
        //
    }
}
