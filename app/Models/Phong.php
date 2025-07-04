<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phong extends Model
{
    use HasFactory;

    protected $fillable = [
        'ma_phong', 'dien_tich', 'mo_ta', 'tang', 'gia', 'hinh_anh', 'trang_thai'
    ];

    public function hopdongs()
    {
        return $this->hasMany(Hopdong::class);
    }
}
