<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    //
    protected $table = 'ratings';
    protected $primaryKey = 'MaDanhGia';
    protected $fillable = [

        'MaNha',
        'MaNguoiDung',
        'SoSao',
        'NoiDung',
        'ThoiGian',
        'LuotThich',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }
    
    public function house()
    {
        return $this->belongsTo(House::class, 'MaNha', 'MaNha');
    }
}
