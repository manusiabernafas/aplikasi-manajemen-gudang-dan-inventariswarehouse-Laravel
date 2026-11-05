# WMS Nexus — Sistem Manajemen Gudang & Inventaris

Sistem Warehouse Management System (WMS) berbasis Laravel 11 + Livewire 3 + Tailwind CSS v4, untuk kebutuhan pergudangan multi-lokasi: penerimaan barang, quality control, transfer antar gudang, stock opname, hingga pengiriman ke customer.

## Fitur Utama
- Dashboard KPI real-time (SKU aktif, valuasi stok, peringatan stok rendah/kosong)
- Manajemen Multi-Gudang & Lokasi Rak
- Manajemen Produk multi-satuan dengan barcode auto-generate
- Manajemen Kategori, Supplier, dan Customer
- Purchase Order dengan alur Receiving & Quality Control per item
- Stock Batch & Expiry Tracking, metode costing FIFO/Average
- Stock Transfer antar gudang (atomic transaction)
- Stock Opname dengan alur freeze → hitung fisik → selesaikan
- Sales Order → Picking List (rute rak terdekat) → Surat Jalan (Delivery Order)
- Role & Permission (Admin, Warehouse Manager, Purchasing, Picker)
- Audit Log untuk seluruh transaksi stok

## Instalasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Buat database MySQL lalu isi kredensial di `.env`, kemudian:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Akses di `http://localhost:8000`. Login demo: `admin@wms.test` / `password`.
