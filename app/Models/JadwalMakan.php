<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class JadwalMakan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_makans';

    protected $fillable = [
        'user_id',
        'jam',
        'keterangan',
    ];

    public function logs()
    {
        return $this->hasMany(LogMakan::class, 'jadwal_makan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}