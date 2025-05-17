<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $table = 'houses';
    protected $primaryKey = 'MaNha';
    public $timestamps = true;
    protected $fillable = [
        'TieuDe',
        'DiaChi',
        'Gia',
        'SoPhongNgu',
        'SoPhongTam',
        'DienTich',
        'SoTang',
        'HinhAnh',
        'TrangThai',
        'NoiBat',
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
}
