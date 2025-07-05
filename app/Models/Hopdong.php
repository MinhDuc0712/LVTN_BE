<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hopdong extends Model
{
    use HasFactory;

    protected $fillable = ['phong_id', 'khach_id', 'ngay_bat_dau', 'ngay_ket_thuc', 'tien_coc','tien_thue','chi_phi_tien_ich','ghi_chu'];

    public function phong()
    {
        return $this->belongsTo(Phong::class);
    }

    public function khach()
    {
        return $this->belongsTo(Khach::class);
    }

    public function phieudiens()
    {
        return $this->hasMany(Phieudien::class);
    }

    public function phienuocs()
    {
        return $this->hasMany(Phieunuoc::class);
    }


    public function phieuthutiens()
    {
        return $this->hasMany(Phieuthutien::class);
    }

    public function phieutraientro()
    {
        return $this->hasOne(Phieutratientro::class);
    }
}
