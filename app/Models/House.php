<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class House extends Model
{
    const STATUS_PENDING_PAYMENT = 'Đang chờ thanh toán';
    const STATUS_PROCESSING = 'Đang xử lý';
    const STATUS_APPROVED = 'Đã duyệt';
    const STATUS_REJECTED = 'Đã từ chối';
    const STATUS_RENTED = 'Đã cho thuê';
    const STATUS_HIDDEN = 'Đã ẩn';
    const STATUS_EXPIRED = 'Tin hết hạn';
    protected $table = 'houses';
    protected $primaryKey = 'MaNha';
    public $timestamps = false;
    protected $fillable = ['TieuDe', 'Tinh_TP', 'Quan_Huyen', 'Phuong_Xa', 'Duong', 'DiaChi', 'Gia', 'SoPhongNgu', 'SoPhongTam', 'DienTich', 'SoTang', 'HinhAnh', 'TrangThai', 'NoiBat', 'MoTaChiTiet', 'NgayHetHan', 'MaNguoiDung', 'MaDanhMuc'];

    public function scopeApproved($query)
    {
        return $query->where('TrangThai', self::STATUS_APPROVED);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('MaNguoiDung', $userId);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->Duong}, {$this->Phuong_Xa}, {$this->Quan_Huyen}, {$this->Tinh_TP}";
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }
    public function category()
    {
        return $this->belongsTo(Categories::class, 'MaDanhMuc', 'MaDanhMuc');
    }

    public function utilities()
    {
        return $this->belongsToMany(Utilities::class, 'house_utility', 'MaNha', 'MaTienIch');
    }
    public function images()
    {
        return $this->hasMany(Images::class, 'MaNha', 'MaNha');
    }
}
