<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phieudien extends Model
{
    use HasFactory;

    protected $fillable = ['hopdong_id', 'chi_so_dau', 'chi_so_cuoi', 'don_gia', 'thang'];

    public function hopdong()
    {
        return $this->belongsTo(Hopdong::class);
    }
}
