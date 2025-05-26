<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class House extends Model
{
    protected $table = 'houses';
    protected $primaryKey = 'MaNha';
    public $timestamps = true;
    protected $fillable = [
        'TieuDe',
        'Tinh_TP',
        'Quan_Huyen',
        'Phuong_xa',
        'Duong',
        'DiaChi',
        'Gia',
        'SoPhongNgu',
        'SoPhongTam',
        'DienTich',
        'SoTang',
        'HinhAnh',
        'TrangThai',
        'NoiBat',
        'MoTaChiTiet',
        'MaNguoiDung'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'MaChuNha', 'MaNguoiDung');
    }
    public function categories()
    {
        return $this->belongsTo(Categories::class, 'MaLoaiNha', 'MaLoaiNha');
    }
    public function utilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Utilities::class,
            'house_utility',
            'MaNha',
            'MaTienIch'
        );
    }
}
