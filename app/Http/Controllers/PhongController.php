<?php

namespace App\Http\Controllers;

use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PhongImage;
class PhongController extends Controller
{
    public function index()
    {
        $rooms = Phong::with('images')->get();

        return response()->json([
            'data' => $rooms
        ]);
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

    public function show($id)
    {
        $phong = Phong::with('images')->find($id);
        if (!$phong) {
            return response()->json(['message' => 'Phòng không tồn tại'], 404);
        }
        return response()->json($phong);
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
    public function destroyImage($id)
    {
        $image = PhongImage::findOrFail($id);
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return response()->json(['message' => 'Đã xoá ảnh'], 200);
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
