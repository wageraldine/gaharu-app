# DOKUMENTASI FITUR APLIKASI GAHARU

## Bahasa & Teknologi
- **Backend**: PHP (Laravel 8+)
- **Frontend**: Blade Templates, JavaScript (Alpine.js), Tailwind CSS
- **Database**: SQLite (via Laravel Eloquent ORM)
- **Autentikasi**: Laravel Breeze (Built-in)

---

## DAFTAR HALAMAN DAN FITUR

### 1. **Halaman Login & Registrasi**
- **Route**: `/login`, `/register`
- **Controller**: `App\Http\Controllers\Auth\*` (Laravel Breeze)
- **View**: `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`
- **Fitur**:
  - Login dengan email/password
  - Registrasi akun baru
  - Lupa password
  - Verifikasi email

### 2. **Dashboard (Setelah Login)**
- **Route**: `/dashboard`
- **View**: `resources/views/dashboard.blade.php`
- **Controller**: Closure di `routes/web.php` (Baris 23-27)
- **Fitur**:
  - Menampilkan semua produk
  - Slider gambar produk (drag/swipe)
  - Tombol keranjang dengan badge counter
  - Tombol "Tambah Produk" (hanya untuk Super Admin)
- **Lokasi Kode**:
  - Logika produk: `routes/web.php:24`
  - Slider produk: `resources/views/dashboard.blade.php:40-98`
  - Counter keranjang: `resources/views/dashboard.blade.php:14-22`

### 3. **Halaman Tambah/Edit Produk (Super Admin)**
- **Route**: 
  - GET `/produk/tambah` → Tambah produk
  - GET `/edit/{id}` → Edit produk
  - POST `/produk/tambah` → Simpan produk
  - POST `/update/{id}` → Update produk
- **View**: 
  - `resources/views/produk/tambah.blade.php`
  - `resources/views/edit.blade.php`
- **Controller**: Closure di `routes/web.php` (Baris 62-153)
- **Fitur**:
  - Upload hingga 5 gambar per produk
  - Input nama, harga, deskripsi
  - Validasi gambar (JPG, PNG, WEBP, max 2MB)
  - Preview gambar sebelum upload
- **Lokasi Kode**:
  - Form tambah: `resources/views/produk/tambah.blade.php`
  - Validasi: `routes/web.php:67-76`
  - Upload gambar: `routes/web.php:78-85`

### 4. **Keranjang Belanja**
- **Route**:
  - GET `/cart` → Tampilkan keranjang
  - POST `/cart/add/{id}` → Tambah produk
  - POST `/cart/remove/{id}` → Hapus produk
- **View**: `resources/views/cart.blade.php`
- **Controller**: Closure di `routes/web.php` (Baris 29-61)
- **Fitur**:
  - Menampilkan produk di keranjang
  - Update jumlah produk
  - Hitung total harga
  - Tombol "Buat Pesanan"
- **Lokasi Kode**:
  - Logika keranjang: `routes/web.php:29-61`
  - Tabel keranjang: `resources/views/cart.blade.php:33-75`

### 5. **Halaman Pesanan**
- **Route**:
  - GET `/orders` → Tampilkan pesanan
  - POST `/orders` → Buat pesanan
  - POST `/orders/{order}/proof` → Upload bukti pembayaran
- **Controller**: `App\Http\Controllers\OrderController`
- **View**: `resources/views/orders/index.blade.php`
- **Fitur**:
  - Daftar pesanan dengan status
  - Upload bukti pembayaran
  - Update status pesanan (Admin)
  - Info rekening bank
- **Lokasi Kode**:
  - Order model: `app/Models/Order.php`
  - Status label/color: `app/Models/Order.php:34-54`
  - Form pesanan: `resources/views/cart.blade.php:91-200`

### 6. **Profile Pengguna**
- **Route**: `/profile` (GET, PATCH, DELETE)
- **Controller**: `App\Http\Controllers\ProfileController`
- **View**: `resources/views/profile/edit.blade.php`
- **Fitur**:
  - Update profil
  - Ganti password
  - Hapus akun
- **Lokasi Kode**:
  - Form edit: `resources/views/profile/partials/update-profile-information-form.blade.php`
  - Password: `resources/views/profile/partials/update-password-form.blade.php`

### 7. **User Management (Super Admin)**
- **Route**: `/admin/users/*` (prefix admin)
- **Controller**: `App\Http\Controllers\UserController`
- **View**: `resources/views/admin/users/*.blade.php`
- **Fitur**:
  - Lihat semua user
  - Tambah user
  - Edit user
  - Hapus user
- **Lokasi Kode**:
  - User model: `app/Models/User.php`
  - Controller: `app/Http/Controllers/UserController.php`

---

## CARA MENGUBAH KONTEN

### 1. **Mengganti Gambar Produk**

**Cara 1: Lewat Form Edit Produk**
1. Login sebagai Super Admin
2. Klik produk yang ingin diubah
3. Upload gambar baru
4. Simpan perubahan

**Cara 2: Upload Manual ke Storage**
1. Gambar disimpan di: `storage/app/public/products/`
2. Akses via: `public/storage/products/`
3. Default gambar: `storage/app/public/products/logo_gaharu.jpg`

### 2. **Mengganti Harga Produk**

**Lewat Form Edit:**
1. Di dashboard, klik produk yang ingin diubah
2. Edit harga di kolom "Harga"
3. Simpan perubahan

**Langsung di Database:**
```sql
UPDATE products SET price = 5000000 WHERE id = 1;
```

### 3. **Mengganti Nama/Deskripsi Produk**

**Lewat Form Edit:**
1. Edit di kolom "Nama Produk" dan "Deskripsi"
2. Simpan perubahan

### 4. **Mengganti Judul Halaman & Logo**

**Header Dashboard:**
- File: `resources/views/dashboard.blade.php`
- Baris 5: `🕌 Arabian Oud Collection`

**Navigation Bar:**
- File: `resources/views/layouts/navigation.blade.php`
- Ubah logo dan menu sesuai kebutuhan

### 5. **Mengganti Info Rekening Bank**

**Lokasi:** `resources/views/orders/index.blade.php`
- Baris 26-29:
```php
<div><span class="font-medium">Bank</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span class="font-semibold">Bank Central Asia (BCA)</span></div>
<div><span class="font-medium">No. Rekening</span> : <span class="font-semibold tracking-widest">400011223344</span></div>
<div><span class="font-medium">Atas Nama</span>&nbsp;&nbsp; : <span class="font-semibold">PT Gaharu Indonesia</span></div>
```

### 6. **Mengganti Status Pesanan**

**Status Available:**
- `pending_payment` → Menunggu Pembayaran
- `waiting_confirmation` → Menunggu Konfirmasi
- `paid` → Lunas
- `cancelled` → Dibatalkan

**Update via Database:**
```sql
UPDATE orders SET status = 'paid' WHERE id = 1;
```

**Update di Controller:**
- File: `app/Http/Controllers/OrderController.php`
- Method: `markPaid()`, `cancel()`

### 7. **Menambah Produk Baru**

1. Akses dashboard
2. Klik "Tambah Produk" (Super Admin)
3. Isi form:
   - Nama produk
   - Harga (tanpa titik/koma)
   - Deskripsi
   - Upload gambar (opsional)
4. Klik "Simpan Produk"

---

## LOKASI FILE UTAMA

### Routes
- **Main routes**: `routes/web.php`
- **Auth routes**: `routes/auth.php`

### Controllers
- **Order**: `app/Http/Controllers/OrderController.php`
- **User**: `app/Http/Controllers/UserController.php`
- **Profile**: `app/Http/Controllers/ProfileController.php`

### Models
- **Product**: `app/Models/Product.php`
- **Order**: `app/Models/Order.php`
- **User**: `app/Models/User.php`

### Views
- **Dashboard**: `resources/views/dashboard.blade.php`
- **Cart**: `resources/views/cart.blade.php`
- **Orders**: `resources/views/orders/index.blade.php`
- **Add Product**: `resources/views/produk/tambah.blade.php`
- **Edit Product**: `resources/views/edit.blade.php`

### Storage
- **Gambar produk**: `storage/app/public/products/`
- **Public symlink**: `public/storage` → `storage/app/public`

---

## DATABASE TABEL UTAMA

Database aplikasi menggunakan SQLite dengan lokasi file:
- **File Database**: `database/database.sqlite`
- **Schema**: Laravel Migration (file di `database/migrations/`)
- **Seeding**: Sample data (file di `database/seeders/`)

### products
```sql
id (PK), name, description, price, images (JSON), created_at, updated_at
```

### orders
```sql
id (PK), user_id (FK), items (JSON), total, status, payment_proof, notes, order_number, created_at, updated_at
```

### users
```sql
id (PK), name, email, email_verified_at, password, remember_token, created_at, updated_at
```

### Database Operations

**Lihat struktur database:**
```bash
php artisan tinker --execute="print_r(\DB::select('PRAGMA table_info(products)'));"
```

**Backup SQLite:**
```bash
cp database/database.sqlite database/database_backup_$(date +%Y%m%d).sqlite
```

**Restore SQLite:**
```bash
cp database/database_backup_20240421.sqlite database/database.sqlite
```

**Hapus semua data (development):**
```bash
php artisan db:wipe --force
php artisan migrate --fresh
```

---

## CATATAN PENTING

1. **Super Admin**: User dengan role super admin bisa mengakses semua fitur manajemen
2. **Gambar Default**: Jika tidak upload gambar, otomatis pakai `logo_gaharu.jpg`
3. **Session Cart**: Data keranjang tersimpan di session dan juga di database user
4. **Order Number**: Format otomatis `Order/DD/MM/YYYY/SEQ`
5. **File Upload**: Max 5 gambar, max 2MB per gambar
6. **Image Formats**: JPG, PNG, WEBP
7. **Database**: Menggunakan SQLite, file terletak di `database/database.sqlite`

---

## TAMBAHAN FITUR

### Frontend Component ( reusable )
- **Layout**: `resources/views/layouts/`
- **Components**: `resources/views/components/`
- **Tailwind CSS**: Konfigurasi di `tailwind.config.js`

### JavaScript
- **Alpine.js**: Untuk interaksi frontend
- **Custom JS**: Sisipkan di dalam tag `<script>` di view
- **Event Listeners**: Gunakan `@` directive Alpine.js

---

## UPDATE PANDUAN

Documentation ini perlu diupdate jika:
- Menambah fitur baru
- Merubah struktur route/controller
- Menambah tabel database baru
- Merubah format data atau status