# Backtracking Visualization Feature - Documentation

## Overview
Fitur visualisasi backtracking menampilkan proses pencarian solusi secara real-time dalam bentuk tree diagram. Halaman ini dirancang untuk mendemonstrasikan algoritma backtracking dengan visualisasi yang intuitif dan responsif.

## URLs
- **Main Page**: `/backtracking` - Halaman utama visualisasi backtracking
- **API Endpoint**: `/api/backtracking/visualize` - API untuk data real-time (untuk future enhancement)

## File Structure
```
app/Http/Controllers/
├── BacktrackingController.php      # Controller untuk handle backtracking logic

resources/views/
├── backtracking/
│   └── index.blade.php             # Main visualization page
├── components/
│   └── backtracking-tree.blade.php # Reusable tree component
└── layouts/
    └── navigation.blade.php        # Navigation dengan link ke backtracking
```

## Components

### BacktrackingController
- `index()` - Menampilkan halaman visualisasi dengan sample data
- `visualize()` - API endpoint untuk mendapatkan data backtracking real-time

### View: backtracking/index.blade.php
- Header dengan judul dan deskripsi
- Control panel dengan status indicators
- Main visualization area dengan SVG tree
- Legend untuk menjelaskan node types
- Debug info (hanya di mode development)

### Component: backtracking-tree.blade.php
- Reusable component untuk rendering tree
- Menerima `nodes` dan `edges` sebagai props
- Menggunakan SVG untuk rendering grafis

## Data Structure

### Backtracking Data Format
```php
$backtrackingData = [
    'title' => 'Real-time Engine Trace Log (Backtracking Tree)',
    'processId' => 'unique-process-id',
    'nodes' => [
        [
            'id' => 'unique-id',
            'type' => 'start|process|error',      // Tipe node
            'label' => 'Node Label',
            'description' => 'Node Description',   // Optional
            'status' => 'active|completed|error',
            'level' => 0,                          // Depth level
        ],
        // ... more nodes
    ],
    'edges' => [
        ['from' => 'node-id', 'to' => 'node-id'],
        // ... more connections
    ]
];
```

## Node Types
1. **start** - Node awal dari proses backtracking
   - Warna: Biru (#6366F1)
   - Icon: Lingkaran hijau (active indicator)

2. **process** - Node untuk tahap proses
   - Warna: Abu-abu (#374151)
   - Menampilkan label dan deskripsi

3. **error** - Node untuk error/warning
   - Warna: Abu-abu dengan dashed border
   - Format italic untuk pesan error

## Styling
Menggunakan Tailwind CSS dengan:
- Dark theme (slate color palette)
- Gradient background
- Responsive layout (mobile-first)
- Smooth transitions dan animations

## Future Enhancements
1. **WebSocket Integration** - Real-time data streaming
2. **Interactive Nodes** - Click untuk expand/collapse branches
3. **Zoom & Pan** - Untuk tree yang besar
4. **Timeline Control** - Play/pause/slow-motion untuk trace
5. **Export** - Download as image atau JSON
6. **Performance Metrics** - Timing dan complexity analysis

## Usage Example

### Basic Usage
```blade
<!-- Access halaman -->
<a href="{{ route('backtracking.index') }}">Lihat Backtracking</a>
```

### Custom Data
```php
// Di Controller
$backtrackingData = [
    'title' => 'Custom Backtracking Process',
    'processId' => 'custom-process-123',
    'nodes' => [
        // Tambahkan nodes sesuai kebutuhan
    ],
    'edges' => [
        // Tambahkan connections
    ]
];

return view('backtracking.index', compact('backtrackingData'));
```

### Using Tree Component
```blade
<x-backtracking-tree 
    :nodes="$nodes" 
    :edges="$edges" 
/>
```

## Development Notes
- Component sudah siap untuk integrasi dengan real backtracking algorithm
- SVG rendering memungkinkan scaling tanpa quality loss
- Debug info dapat dimatikan di production dengan `config('app.debug')`
- Component design memudahkan untuk adding interactivity di masa depan

## Browser Compatibility
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance
- SVG rendering optimal untuk struktur tree
- CSS animations menggunakan GPU acceleration
- No external JS dependencies (selain Alpine untuk future interactivity)
