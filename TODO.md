# TODO - Dark Mode Feature

- [ ] Tambahkan toggle dark mode di `resources/views/layouts/app.blade.php`.
  - [ ] Simpan pilihan ke `localStorage`.
  - [ ] Default mengikuti OS via `prefers-color-scheme`.
- [ ] Update base styling layout (`resources/views/layouts/app.blade.php`) agar background, teks, border, dan efek hover berubah saat `.dark` aktif.
- [ ] Update `resources/views/dashboard.blade.php` agar elemen yang sebelumnya hardcoded light (bg-white/text-gray-*) punya padanan dark (`dark:*`).
- [ ] Tambahkan/rapikan aturan umum di `resources/css/app.css` untuk transisi warna.
- [ ] Verifikasi: refresh halaman, pastikan semua halaman memakai `<x-app-layout>` ikut berubah sesuai dark mode.

