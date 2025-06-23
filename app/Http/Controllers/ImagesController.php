<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Illuminate\Http\Request;
use App\Models\House;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function uploadHouseImages(Request $request, $houseId)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ]);

        $house = House::findOrFail($houseId);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $fileName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/houses/images', $fileName);

            $imageModel = new Images([
                'MaNha' => $house->MaNha,
                'DuongDan' => Storage::url($path),
            ]);
            $imageModel->save();

            $uploadedImages[] = $imageModel;
        }

        return response()->json([
            'success' => true,
            'images' => $uploadedImages,
        ]);
    }

    public function getHouseImages($houseId)
    {
        $images = Images::where('MaNha', $houseId)
            ->orderBy('LaAnhDaiDien', 'DESC')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->map(function ($image) {
                return [
                    'MaHinhAnh' => $image->MaHinhAnh,
                    'url' => $image->DuongDanHinh,
                    'LaAnhDaiDien' => $image->LaAnhDaiDien,
                    'created_at' => $image->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $images,
        ]);
    }

    // Xóa ảnh
    public function deleteHouseImage($houseId, $imageId)
    {
        $image = Images::where('MaNha', $houseId)->where('MaHinhAnh', $imageId)->firstOrFail();
        // Xóa record database
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ảnh thành công',
        ]);
    }

    // Đặt làm ảnh đại diện
    public function setMainImage($houseId, $imageId)
    {
        // Bỏ đặt tất cả ảnh đại diện cũ
        Images::where('MaNha', $houseId)->update(['LaAnhDaiDien' => false]);

        // Đặt ảnh mới làm đại diện
        $image = Images::where('MaNha', $houseId)->where('MaHinhAnh', $imageId)->firstOrFail();

        $image->update(['LaAnhDaiDien' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đặt ảnh làm đại diện',
        ]);
    }
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Images $images)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Images $images)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Images $images)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($imageId)
    {
        $image = Images::findOrFail($imageId);
        $house = House::findOrFail($image->MaNha);
        if ($house->MaNguoiDung !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền thao tác'], 403);
        }

        if ($image->DuongDanHinh) {
            Storage::delete(str_replace('/storage/', 'public/', $image->DuongDanHinh));
        }

        $image->delete();

        return response()->json(['success' => true]);
    }
    public function list($houseId)
    {
        $house = House::findOrFail($houseId);
        if ($house->MaNguoiDung !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền xem ảnh'], 403);
        }

        $images = Images::where('MaNha', $houseId)->get();

        return response()->json(['images' => $images]);
    }

    public function setThumbnail($imageId)
    {
        $image = Images::findOrFail($imageId);
        $house = House::findOrFail($image->MaNha);
        if ($house->MaNguoiDung !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền thao tác'], 403);
        }

        Images::where('MaNha', $house->MaNha)->update(['HinhDaiDien' => false]);
        $image->HinhDaiDien = true;
        $image->save();

        return response()->json(['message' => 'Đã đặt làm ảnh đại diện']);
    }
}
