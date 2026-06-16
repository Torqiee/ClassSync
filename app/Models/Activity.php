<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; 

class Activity extends Model
{
    // Nama collection di MongoDB
    protected $collection = 'activities';

    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'kategori',      // Kuliah, Organisasi, UKM, Tugas
        'tipe',          // 'tetap' (hard constraint) atau 'fleksibel' (tugas)
        'hari',          // Senin, Selasa, dst.
        'jam_mulai',     // 08:00
        'jam_selesai',   // 10:00
        'deadline',      // Hari batas akhir untuk tugas fleksibel
        'durasi',        // Berapa jam (untuk perhitungan AI)
        'is_scheduled',   // boolean: true jika AI sudah menemukan jadwalnya
        'status', 
        'diselesaikan_pada'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}