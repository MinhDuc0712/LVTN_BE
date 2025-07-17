<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phieudien extends Model
{
    use HasFactory;
    protected $table = 'phieudien';
     public $timestamps = false;
    protected $fillable = ['hopdong_id', 'chi_so_dau', 'chi_so_cuoi', 'don_gia','ngay_tao',  'thang','trang_thai'];

    public function hopdong()
    {
        return $this->belongsTo(Hopdong::class);
    }
}
