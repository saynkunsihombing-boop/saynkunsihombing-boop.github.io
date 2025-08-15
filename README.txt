Yayasan Jalan Harapan Indonesia - Simple PHP + MySQL Project
------------------------------------------------------------
Struktur:
/yayasan_project/
- index.php           -> Halaman publik (menampilkan berita dari DB)
- login.php           -> Form login editor
- dashboard.php       -> Dashboard editor (CRUD berita)
- add_news.php        -> Tambah berita (editor)
- edit_news.php       -> Edit berita (editor)
- delete_news.php     -> Hapus berita (editor)
- logout.php          -> Logout
- config.php          -> Koneksi DB & inisialisasi tabel + default admin
- style.css           -> Gaya tampilan
- uploads/            -> Folder untuk gambar berita (pastikan writable)
- install.sql         -> SQL untuk membuat database (optional)

Cara cepat menggunakan (di local menggunakan XAMPP / Laragon / MAMP):
1. Salin folder ini ke folder web server Anda (mis. htdocs atau www).
2. Buat database MySQL bernama 'yayasan_db' atau ubah pengaturan di config.php.
3. Buka browser: akses http://localhost/yayasan_project/
4. Halaman publik akan membuat tabel jika belum ada. Default akun editor dibuat otomatis:
   Username: editor
   Password: password123
5. Login editor melalui link 'Login Editor' -> kelola berita di Dashboard.

Keamanan & Catatan:
- Ini contoh sederhana. Untuk produksi, gunakan SSL, validasi yang lebih ketat, CSRF token, sanitasi file upload,
  batasi tipe file, dan simpan password dengan PHP password_hash (sudah digunakan).
- Pastikan folder 'uploads' punya permission tulis.
- Untuk mengganti password editor, akses database dan update field password dengan password_hash PHP, 
  atau gunakan dashboard tambahan yang Anda buat.
