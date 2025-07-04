<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phieuthutien extends Model
{
    use HasFactory;

    protected $fillable = ['hopdong_id', 'so_tien', 'ngay_thu', 'noi_dung'];

    public function hopdong()
    {
        return $this->belongsTo(Hopdong::class);
    }
}
