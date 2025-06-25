<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite_House extends Model
{
    //
    protected $table = 'favourite_houses';
    protected $primaryKey = 'MaYeuThich';
    protected $fillable = ['MaYeuThich', 'MaNha', 'MaNguoiDung'];

    public function house()
    {
        return $this->belongsTo(House::class, 'MaNha', 'MaNha');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }
}
