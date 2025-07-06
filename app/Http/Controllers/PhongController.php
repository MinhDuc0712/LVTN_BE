<?php

namespace App\Http\Controllers;

use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhongController extends Controller
{

    public function index()
    {

        return Phong::with('images')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ten_phong' => 'required|string|max:50|unique:phong,ten_phong',
            'dien_tich' => 'required|numeric|min:1',
            'tang' => 'required|integer|min:0',
            'gia' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'in:trong,da_thue,bao_tri',
        ]);

        $phong = Phong::create($data);

        return response()->json(['data' => $phong], 201);
    }
    public function uploadImages(Request $request, Phong $phong)
    {
        \Log::info('FILES DEBUG', $request->allFiles());

        $request->validate([
            'hinh_anh' => 'required',
            'hinh_anh.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        foreach ($request->file('hinh_anh') as $file) {
            $path = $file->store('phongs', 'public');
            $phong->images()->create(['image_path' => $path]);
        }

        return response()->json(['message' => 'Upload OK', 'images' => $phong->images], 201);
    }


    public function show(Phong $phong)
    {
        return $phong->load('images');
    }


    public function update(Request $request, Phong $phong)
    {
        $data = $request->validate([
            'ten_phong' => 'sometimes|string|max:50|unique:phong,ten_phong,' . $phong->id,
            'dien_tich' => 'sometimes|numeric|min:1',
            'tang' => 'sometimes|integer|min:0',
            'gia' => 'sometimes|numeric|min:0',
            'mo_ta' => 'sometimes|nullable|string',
            'trang_thai' => 'sometimes|in:trong,da_thue,bao_tri',
        ]);

        $phong->update($data);

        // Có thể cho phép thêm ảnh mới
        if ($request->hasFile('hinh_anh')) {
            foreach ($request->file('hinh_anh') as $file) {
                $path = $file->store('phongs', 'public');
                $phong->images()->create(['image_path' => $path]);
            }
        }

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $phong->load('images'),
        ]);
    }

    public function destroy(Phong $phong)
    {
        foreach ($phong->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $phong->delete();

        return response()->json(['message' => 'Đã xoá phòng'], 204);
    }
}