<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phieuthutien extends Model
{
    use HasFactory;
    protected $table = 'phieuthutien';
    public $timestamps = false;
    protected $fillable = ['hopdong_id','thang', 'so_tien','da_thanh_toan','no', 'ngay_thu','trang_thai', 'noi_dung'];

    public function hopdong()
    {
        return $this->belongsTo(Hopdong::class);
    }
}
