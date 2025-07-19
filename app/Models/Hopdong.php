<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hopdong extends Model
{
    use HasFactory;
    protected $table = 'hopdong';
    protected $fillable = ['phong_id', 'khach_id', 'ngay_bat_dau', 'ngay_ket_thuc', 'tien_coc','tien_thue','chi_phi_tien_ich','ghi_chu'];
    public $timestamps = false;

    public function phong()
    {
        return $this->belongsTo(Phong::class);
    }

    public function khach()
    {
        return $this->belongsTo(Khach::class);
    }

    public function phieudien()
    {
        return $this->hasMany(Phieudien::class);
    }

    public function phienuoc()
    {
        return $this->hasMany(Phieunuoc::class);
    }


    public function phieuthutien()
    {
        return $this->hasMany(Phieuthutien::class);
    }

    public function phieutraientro()
    {
        return $this->hasOne(Phieutratientro::class);
    }
}
