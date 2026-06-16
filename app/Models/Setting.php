<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class Setting extends Model
{
    protected $collection = 'settings';

    protected $fillable = [
        'user_id',
        'istirahat_1_jam',
        'maks_3_kegiatan',
        'waktu_produktif',
    ];

    // Secara default, tipe data dari database form adalah string, kita cast ke boolean
    protected $casts = [
        'istirahat_1_jam' => 'boolean',
        'maks_3_kegiatan' => 'boolean',
        'waktu_produktif' => 'boolean',
    ];
}