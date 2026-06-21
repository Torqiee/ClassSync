<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class BacktrackingController extends Controller
{
    public function index()
    {
        // Simulasi data backtracking tree
        // Dalam implementasi real, data ini akan datang dari service yang menjalankan backtracking algorithm
        
        $backtrackingData = [
            'title' => 'Real-time Engine Trace Log (Backtracking Tree)',
            'processId' => 'hitung-bobot-mata-kuliah',
            'nodes' => [
                [
                    'id' => 'start',
                    'type' => 'start',
                    'label' => 'START: HITUNG BOBOT MATA KULIAH',
                    'status' => 'active',
                    'icon' => '●',
                    'level' => 0,
                ],
                [
                    'id' => 'process-1',
                    'type' => 'process',
                    'label' => 'PROSES 1',
                    'description' => 'Sortir Mata Kuliah berdasarkan Bobot Tertinggi & Buat Tabel Hubungan Tetangga',
                    'status' => 'completed',
                    'level' => 1,
                ],
                [
                    'id' => 'error-1',
                    'type' => 'error',
                    'label' => 'ERROR',
                    'description' => 'Belum ada variabel kegiatan/tugas dalam antrian domain.',
                    'status' => 'error',
                    'level' => 2,
                ],
            ],
            'edges' => [
                ['from' => 'start', 'to' => 'process-1'],
                ['from' => 'process-1', 'to' => 'error-1'],
            ]
        ];

        return view('backtracking.index', compact('backtrackingData'));
    }

    public function visualize()
    {
        // Endpoint untuk API real-time backtracking data
        // Bisa digunakan untuk WebSocket atau polling
        
        $data = $this->getBacktrackingState();
        
        return response()->json($data);
    }

    private function getBacktrackingState()
    {
        // Placeholder untuk mendapatkan state backtracking
        return [
            'status' => 'running',
            'nodes' => [],
            'timestamp' => now(),
        ];
    }
}
