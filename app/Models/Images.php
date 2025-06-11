<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    //
    protected $table = 'images';
     protected $primaryKey = 'MaHinhAnh';
    protected $fillable = ['MaNha', 'DuongDanHinh', 'LaAnhDaiDien'];

    //  public function getUrlAttribute()
    // {
    //     return asset('storage/house_images/' . $this->ten_file);
    // }

    public function houses()
    {
        return $this->belongsTo(House::class, 'MaNha', 'MaNha');
    }
    
}
