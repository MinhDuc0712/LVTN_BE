<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Images;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class HouseController extends Controller
{
    const STATUS_PENDING_PAYMENT = 'Đang chờ thanh toán';
    const STATUS_PROCESSING = 'Đang xử lý';
    const STATUS_APPROVED = 'Đã duyệt';
    const STATUS_REJECTED = 'Đã từ chối';
    const STATUS_RENTED = 'Đã cho thuê';
    const STATUS_HIDDEN = 'Đã ẩn';
    const STATUS_EXPIRED = 'Tin hết hạn';
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // Khởi tạo query builder
        $query = House::query()
            ->with(['images', 'utilities', 'user', 'category'])
            ->where('TrangThai', self::STATUS_APPROVED);

        // Lọc theo giá
        if ($request->filled('price')) {
            switch ($request->price) {
                case 'under-1m':
                    $query->where('Gia', '<', 1000000);
                    break;
                case '1-2m':
                    $query->whereBetween('Gia', [1000000, 2000000]);
                    break;
                case '2-3m':
                    $query->whereBetween('Gia', [2000000, 3000000]);
                    break;
                case '3-5m':
                    $query->whereBetween('Gia', [3000000, 5000000]);
                    break;
                case '5-7m':
                    $query->whereBetween('Gia', [5000000, 7000000]);
                    break;
                case '7-10m':
                    $query->whereBetween('Gia', [7000000, 10000000]);
                    break;
                case '10-15m':
                    $query->whereBetween('Gia', [10000000, 15000000]);
                    break;
                case 'over-15m':
                    $query->where('Gia', '>', 15000000);
                    break;
            }
        }

        // Lọc theo diện tích
        if ($request->filled('area')) {
            switch ($request->area) {
                case 'under-20':
                    $query->where('DienTich', '<', 20);
                    break;
                case '20-30':
                    $query->whereBetween('DienTich', [20, 30]);
                    break;
                case '30-50':
                    $query->whereBetween('DienTich', [30, 50]);
                    break;
                case '50-70':
                    $query->whereBetween('DienTich', [50, 70]);
                    break;
                case '70-90':
                    $query->whereBetween('DienTich', [70, 90]);
                    break;
                case 'over-90':
                    $query->where('DienTich', '>', 90);
                    break;
            }
        }

        // Lọc theo tỉnh/thành phố
        if ($request->filled('province')) {
            $query->where('DiaChi', 'like', '%' . $request->province . '%');
        }

        // Lọc theo quận/huyện
        if ($request->filled('district')) {
            $query->where('DiaChi', 'like', '%' . $request->district . '%');
        }

        if ($request->filled('ward')) {
            $query->where('DiaChi', 'like', '%' . $request->ward . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('MaDanhMuc', $request->category_id);
        }

        $results = $query->get();

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    public function featured()
    {
        $houses = House::with(['images', 'utilities', 'user', 'category'])
            ->where('TrangThai', House::STATUS_APPROVED)
            // ->where('NoiBat', 1)
            // ->orderBy('NgayDang', 'desc')
            ->get();

        return response()->json($houses);
    }

    public function getUserHouses(Request $request)
    {
        $user = $request->user();
        $houses = $user
            ->houses()
            ->with(['images', 'utilities', 'category'])
            ->orderBy('NgayDang', 'desc')
            ->get();

        foreach ($houses as $house) {
            if ($house->TrangThai === House::STATUS_APPROVED && $house->NgayHetHan && 
                now()->gt($house->NgayHetHan)) {
                $house->TrangThai = self::STATUS_EXPIRED; 
                $house->save();
            }
        }
        return response()->json([
            'success' => true,
            'data' => $houses,
        ]);
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
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Không xác thực được người dùng'], 401);
        }

        $validated = $request->validate([
            'TieuDe' => 'required|min:30|max:100',
            // 'Tinh_TP' => 'required|string|max:255',
            // 'Quan_Huyen' => 'required|string|max:255',
            // 'Phuong_Xa' => 'required|string|max:255',
            // 'Duong' => 'nullable|string|max:255',
            'DiaChi' => 'required|string|max:255',
            'SoPhongNgu' => 'required|integer|min:0',
            'SoPhongTam' => 'required|integer|min:0',
            'SoTang' => 'nullable|integer|min:0',
            'DienTich' => 'required|numeric|min:0',
            'Gia' => 'required|numeric|min:0',
            'MoTaChiTiet' => 'required|min:50|max:5000',
            'MaDanhMuc' => 'required|exists:categories,MaDanhMuc',
            'images' => 'required|array|min:1',
            'images.*' => 'string',
            'utilities' => 'nullable|array',
            'utilities.*' => 'exists:utilities,MaTienIch',
        ]);

        // Tạo nhà
        $house = House::create([...$validated, 'MaNguoiDung' => $user->MaNguoiDung]);

        // Gắn tiện ích nếu có
        if (!empty($validated['utilities'])) {
            $house->utilities()->sync(array_map('intval', $validated['utilities']));
        }

        // Thêm hình ảnh
        foreach ($validated['images'] as $index => $url) {
            Images::create([
                'MaNha' => $house->MaNha,
                'DuongDanHinh' => $url,
                'LaAnhDaiDien' => $index === 0,
            ]);
        }

        return response()->json(
            [
                'message' => 'Nhà và ảnh đã được đăng thành công',
                'house' => $house->load('images'),
            ],
            201,
        );
    }

    public function handlePayment(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'houseId' => 'required|exists:houses,MaNha',
            'planType' => 'required|in:normal,vip',
            'duration' => 'required|integer|min:1',
            'unit' => 'required|in:day,week,month',
            'total' => 'required|numeric|min:0',
        ]);

        $house = House::where('MaNha', $validated['houseId'])->where('MaNguoiDung', $user->MaNguoiDung)->first();

        if (!$house) {
            return response()->json(['message' => 'Không tìm thấy bài đăng hoặc không có quyền'], 403);
        }

        if ($user->so_du < $validated['total']) {
            return response()->json(['message' => 'Số dư không đủ để thanh toán'], 400);
        }

        $user->so_du -= $validated['total'];
        $user->save();

        $unitMap = [
            'day' => 1,
            'week' => 7,
            'month' => 30,
        ];

        $days = $validated['duration'] * $unitMap[$validated['unit']];
        $expiryDate = now()->addDays($days);

        $house->TrangThai = $validated['planType'] === 'vip' ? House::STATUS_APPROVED : House::STATUS_PROCESSING;
        $house->NoiBat = $validated['planType'] === 'vip' ? 1 : 0;
        $house->NgayHetHan = $expiryDate;
        $house->save();

        Payments::create([
            'MaNguoiDung' => $user->MaNguoiDung,
            'MaNha' => $house->MaNha,
            'Voucher' => 0,
            'PhiGiaoDich' => $validated['total'],
            'TongTien' => $validated['total'],
        ]);

        return response()->json([
            'message' => 'Thanh toán thành công',
            'so_du_moi' => $user->so_du,
            'TrangThai' => $house->TrangThai,
            'NoiBat' => $house->NoiBat,
            'NgayHetHan' => $expiryDate,
        ]);
    }

    public function show($id)
    {
        $house = House::with(['images', 'utilities', 'user', 'category'])
            ->where('MaNha', $id)

            // ->where('TrangThai', House::STATUS_APPROVED)
            ->first();

        if (!$house) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy nhà hoặc nhà chưa được phê duyệt',
                ],
                404,
            );
        }
        $soTinDang = House::where('MaNguoiDung', $house->MaNguoiDung)->count();

        $house->user->so_tin_dang = $soTinDang;

        return response()->json([
            'success' => true,
            'data' => $house,
        ]);
    }
    public function getByCategory($categoryId)
    {
        $houses = House::with(['images', 'utilities', 'user', 'category'])
            ->where('MaDanhMuc', $categoryId)
            ->where('TrangThai', House::STATUS_APPROVED)
            ->orderBy('NoiBat', 'desc')
            ->orderBy('NgayDang', 'desc')
            ->get();
        foreach ($houses as $house) {
            if ($house->TrangThai === House::STATUS_APPROVED && $house->NgayHetHan && now()->gt($house->NgayHetHan)) {
                $house->TrangThai = self::STATUS_EXPIRED; 
                $house->save();
            }
        }
        return response()->json([
            'success' => true,
            'data' => $houses,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getAllForAdmin(Request $request)
    {
        $query = House::with(['images', 'utilities', 'user', 'category']);

        // Lọc theo trạng thái
        if ($request->has('status')) {
            $query->where('TrangThai', $request->status);
        }

        // Tìm kiếm theo tiêu đề
        if ($request->has('search')) {
            $query->where('TieuDe', 'like', '%' . $request->search . '%');
        }

        // Sắp xếp
        $sortField = $request->get('sort_field', 'NgayDang');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Phân trang
        $perPage = $request->get('per_page', 10);
        $houses = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $houses->items(),
            'pagination' => [
                'total' => $houses->total(),
                'per_page' => $houses->perPage(),
                'current_page' => $houses->currentPage(),
                'last_page' => $houses->lastPage(),
            ],
        ]);
    }
    public function approve($id)
    {
        $house = House::findOrFail($id);
        $house->NgayDang = now();
        $house->TrangThai = self::STATUS_APPROVED;
        $house->save();

        return response()->json([
            'success' => true,
            'message' => 'Bài đăng đã được phê duyệt',
        ]);
    }

    public function reject(Request $request, $id)
    {
        $house = House::findOrFail($id);
        $house->TrangThai = 'Đã từ chối';
        $house->LyDoTuChoi = $request->input('reason');
        $house->save();

        return response()->json([
            'success' => true,
            'message' => 'Bài đăng đã bị từ chối',
            'data' => $house,
        ]);
    }
    public function hide($id)
    {
        $house = House::findOrFail($id);

        if ($house->TrangThai !== self::STATUS_APPROVED) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Chỉ có thể ẩn bài đăng đang ở trạng thái Đã duyệt',
                ],
                400,
            );
        }

        $house->TrangThai = self::STATUS_HIDDEN;
        $house->save();

        return response()->json([
            'success' => true,
            'message' => 'Bài đăng đã được ẩn',
            'data' => $house,
        ]);
    }
    public function relist($id)
    {
        $user = Auth::user();
        $house = House::where('MaNguoiDung', $user->MaNguoiDung)->find($id);

        if (!$house) {
            return response()->json(['message' => 'Không tìm thấy bài đăng'], 404);
        }

        if ($house->TrangThai === self::STATUS_HIDDEN) {
            // Nếu đang ẩn và còn hạn thì cho duyệt lại
            if (now()->lessThanOrEqualTo($house->NgayHetHan)) {
                // $house->NgayDang = now();
                $house->TrangThai = self::STATUS_APPROVED;
                $house->save();
                return response()->json(['message' => 'Đăng lại thành công (từ trạng thái ẩn)']);
            } else {
                return response()->json(['message' => 'Tin đã hết hạn, vui lòng thanh toán lại'], 400);
            }
        }

        if ($house->TrangThai === self::STATUS_EXPIRED) {
            $house->NgayDang = now();
            // Nếu hết hạn thì cho phép thanh toán lại
            return response()->json(
                [
                    'message' => 'Tin đã hết hạn, bạn cần thanh toán lại để đăng lại',
                    'require_payment' => true,
                ],
                200,
            );
        }

        return response()->json(['message' => 'Chỉ có thể đăng lại tin đã ẩn hoặc hết hạn'], 400);
    }

    public function edit(House $house)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       
        $user = Auth::user();
        $house = House::where('MaNha', $id)->where('MaNguoiDung', $user->MaNguoiDung)->first();

        if (!$house) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy bài đăng hoặc bạn không có quyền chỉnh sửa',
                ],
                404,
            );
        }

        $validated = $request->validate([
            'TieuDe' => 'required|min:30|max:100',
            // 'Tinh_TP' => 'required|string Stuart',
            // 'Quan_Huyen' => 'required|string|max:255',
            // 'Phuong_Xa' => 'required|string|max:255',
            // 'Duong' => 'nullable|string|max:255',
            'DiaChi' => 'required|string|max:255',
            'SoPhongNgu' => 'required|integer|min:0',
            'SoPhongTam' => 'required|integer|min:0',
            'SoTang' => 'nullable|integer|min:0',
            'DienTich' => 'required|numeric|min:0',
            'Gia' => 'required|numeric|min:0',
            'MoTaChiTiet' => 'required|min:50|max:5000',
            'MaDanhMuc' => 'required|exists:categories,MaDanhMuc',
            'NgayDang' => 'date',
            'images' => 'required|array|min:1',
            'images.*' => 'string',
            'utilities' => 'nullable|array',
            'utilities.*' => 'exists:utilities,MaTienIch',
        ]);

        $house->TrangThai = House::STATUS_PROCESSING;
        $house->NgayDang = now();
        $house->update($validated);

        if (!empty($validated['utilities'])) {
            $house->utilities()->sync(array_map('intval', $validated['utilities']));
        } else {
            $house->utilities()->detach();
        }

        Images::where('MaNha', $house->MaNha)->delete();
        foreach ($validated['images'] as $index => $url) {
            Images::create([
                'MaNha' => $house->MaNha,
                'DuongDanHinh' => $url,
                'LaAnhDaiDien' => $index === 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài đăng thành công',
            'data' => $house->load('images', 'utilities'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(House $house)
    {
        //
    }
}
