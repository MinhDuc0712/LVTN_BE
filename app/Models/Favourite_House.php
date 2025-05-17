<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite_House extends Model
{
    //
    protected $table = 'favourite_house';
    protected $fillable = [
        'MaYeuThich',
        'MaNha',
        'MaNguoiDung'
    ];
}
