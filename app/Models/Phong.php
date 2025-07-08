<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phong extends Model
{
    use HasFactory;
    protected $table = 'phong';
    public $timestamps = false;
    protected $fillable = [
        'ten_phong',
        'dien_tich',
        'mo_ta',
        'tang',
        'gia',
        'trang_thai'
    ];

    public function hopdongs()
    {
        return $this->hasOne(Hopdong::class);
    }
    public function images()
    {
        return $this->hasMany(PhongImage::class, 'phong_id');
    }
}
