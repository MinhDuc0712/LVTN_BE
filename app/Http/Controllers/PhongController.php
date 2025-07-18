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
            'data' => $rooms,
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
    public function uploadImages(Request $request, $id)
    {
        $request->validate([
            'urls' => 'required|array',
            'urls.*' => 'url',
        ]);

        $phong = Phong::findOrFail($id);

        foreach ($request->urls as $url) {
            $phong->images()->create(['image_path' => $url]);
        }

        return response()->json(['message' => 'Images saved']);
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
            'ten_phong' => 'sometimes|string|max:50,' . $phong->id,
            'dien_tich' => 'sometimes|numeric|min:1',
            'tang' => 'sometimes|integer|min:0',
            'gia' => 'sometimes|numeric|min:0',
            'mo_ta' => 'sometimes|nullable|string',
            'trang_thai' => 'sometimes|in:trong,da_thue,bao_tri',
            'urls' => 'sometimes|array',
            'urls.*' => 'url',
        ]);

        $phong->update($data);

        // Nếu có thêm ảnh mới qua Cloudinary URLs
        if ($request->has('urls')) {
            foreach ($request->urls as $url) {
                $phong->images()->create(['image_path' => $url]);
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

        if (!str_starts_with($image->image_path, 'http') && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['message' => 'Đã xoá ảnh'], 200);
    }

    public function destroy($id)
    {
        $phong = Phong::find($id);

        if (!$phong) {
            return response()->json(['message' => 'Phòng không tồn tại'], 404);
        }

        foreach ($phong->images as $img) {
            if (!str_starts_with($img->image_path, 'http')) {
                if (Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
            }
            $img->delete();
        }

        $phong->delete();

        return response()->json(['message' => 'Đã xoá phòng thành công'], 204);
    }



}
