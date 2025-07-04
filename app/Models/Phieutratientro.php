<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phieutratientro extends Model
{
    use HasFactory;

    protected $fillable = ['hopdong_id', 'ngay_tra', 'ghi_chu'];

    public function hopdong()
    {
        return $this->belongsTo(Hopdong::class);
    }
}
