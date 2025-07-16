<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\House;
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
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
        ]);

        $category = Categories::create([
            'name' => $request->name,
            'mo_ta' => $request->mo_ta,
        ]);

        return response()->json(
            [
                'message' => 'Thêm danh mục thành công',
                'category' => $category,
            ],
            201,
        );
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
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
        ]);

        $category = Categories::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'mo_ta' => $request->mo_ta,
        ]);

        return response()->json([
            'message' => 'Cập nhật danh mục thành công',
            'category' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (House::where('MaDanhMuc', $id)->exists()) {
            return response()->json(
                [
                    'message' => 'Không thể xoá danh mục vì đang có bài đăng sử dụng.',
                ],
                400,
            );
        }
        $category = Categories::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Xóa danh mục thành công',
        ]);
    }
}
