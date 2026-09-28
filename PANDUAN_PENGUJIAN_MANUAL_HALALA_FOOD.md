# Panduan Lengkap Pengujian Manual Sistem Halala Food (Berdasarkan Harga Riil Pasar 2026)

Dokumen ini berisi panduan langkah demi langkah (_step-by-step_) untuk menguji seluruh alur operasional dan akuntansi bisnis makanan secara manual langsung dari browser aplikasi, menggunakan referensi harga riil pasar Indonesia (_marketplace_ Tokopedia/Shopee dan Pasar Tradisional) tahun 2026.

---

## 📋 Ringkasan Studi Kasus Pengujian

- **Produk**: Ting Ting Susu (Kemasan Standing Pouch 150g / isi 10 pcs ting-ting)
- **Modal Awal Disetor**: **Rp 2.500.000**
- **Target Produksi**: **200 Pouch**
- **Total Biaya Belanja Bahan Baku**: **Rp 1.669.000**
- **Total HPP / Modal Pokok per 1 Pouch**: **Rp 8.345 / pouch**
- **Skema Titip Jual (Konsinyasi)**: 2 Toko Mitra (100 Pouch per toko)
    - **Harga Setor Titip ke Toko**: **Rp 12.500 / pouch** _(Laba UMKM: Rp 4.155 / 33.24%)_
    - **Harga Jual Eceran Toko ke Pembeli**: **Rp 15.000 / pouch** _(Toko untung: Rp 2.500 / 16.67%)_
- **Hasil Akhir**: Seluruh 200 Pouch terjual habis dan disetor lunas oleh kedua toko mitra.

---

## 🛠️ Langkah 0: Reset Database Bersih (Opsional)

Jika ingin memulai pengujian dari database benar-benar kosong:

```bash
php artisan migrate:fresh
```

---

## 1️⃣ Langkah 1: Setup Akun Kas & Rekening Bank

**Menu:** `Buku Kas & Keuangan` (`http://halala-food.test/cash-book`)

1. Klik tombol **"+ Tambah Akun Baru"**.
2. Masukkan data:
    - **Nama Akun / Rekening**: `Kas Tunai Usaha`
    - **Jenis Rekening**: `Rekening Usaha`
    - **Saldo Awal**: `0`
3. Klik **Simpan**.

---

## 2️⃣ Langkah 2: Input Master Bahan Baku (Harga Riil Pasar 2026)

**Menu:** `Bahan Baku & Resep` (`http://halala-food.test/raw-materials`)

Tambahkan 7 bahan baku berikut melalui tombol **"+ Tambah Bahan Baku"**:

| No  | Nama Bahan Baku               | Satuan | Stok Saat Ini | Batas Min. Stok | Estimasi Harga Beli / Satuan | Referensi Pembelian di Pasar / Toko Bahan Kue     |
| :-- | :---------------------------- | :----- | :------------ | :-------------- | :--------------------------- | :------------------------------------------------ |
| 1   | `Kacang Tanah Sangrai`        | `gram` | `14000`       | `1000`          | `45`                         | Rp 45.000 / kg (Beli 14 bungkus @ 1 kg)           |
| 2   | `Susu Bubuk Full Cream`       | `gram` | `6000`        | `1000`          | `90`                         | Rp 90.000 / kg (NZMP/Indomilk Repack 6 kg)        |
| 3   | `Gula Pasir Kristal`          | `gram` | `8000`        | `1000`          | `18`                         | Rp 18.000 / kg (Gulaku/Rose Brand 8 bungkus)      |
| 4   | `Mentega / Margarin`          | `gram` | `2000`        | `500`           | `47.5`                       | Rp 9.500 / sachet 200g (Blue Band 10 sachet)      |
| 5   | `Standing Pouch Klip (150g)`  | `pcs`  | `200`         | `50`            | `700`                        | Rp 35.000 / pak isi 50 pcs (Beli 4 pak = 200 pcs) |
| 6   | `Stiker Label Kemasan Halala` | `pcs`  | `200`         | `50`            | `300`                        | Rp 12.000 / lembar A3+ isi 40 pcs (Cetak 5 lbr)   |
| 7   | `Plastik Seal Satuan (Dalam)` | `pcs`  | `2000`        | `500`           | `30`                         | Rp 30.000 / pak isi 1.000 lembar (Beli 2 pak)     |

---

## 3️⃣ Langkah 3: Input Master Produk & Formulasi Resep (BOM)

### A. Tambah Produk Jadi

**Menu:** `Kelola Produk Makanan` (`http://halala-food.test/products`)

1. Klik **"+ Tambah Produk"**.
2. Masukkan data:
    - **Nama Produk**: `Ting Ting Susu (Pouch 150g / 10 pcs)`
    - **Satuan**: `bungkus`
    - **Harga Titip Konsinyasi**: `12500`
    - **Harga Jual Eceran Toko**: `15000`
    - **Stok Awal di Gudang**: `0`
3. Klik **Simpan**.

### B. Atur Formulasi Resep (Kebutuhan per 1 Pouch 150g)

**Menu:** `Bahan Baku & Resep` (`http://halala-food.test/raw-materials`)

1. Pada kolom kanan **"Formulasi Resep Produk"**, klik tombol **"Atur Resep"** pada produk _Ting Ting Susu_.
2. Masukkan takaran komposisi berikut:
    - **Kacang Tanah Sangrai**: `70` gram _(70g × Rp 45 = Rp 3.150)_
    - **Susu Bubuk Full Cream**: `30` gram _(30g × Rp 90 = Rp 2.700)_
    - **Gula Pasir Kristal**: `40` gram _(40g × Rp 18 = Rp 720)_
    - **Mentega / Margarin**: `10` gram _(10g × Rp 47,5 = Rp 475)_
    - **Standing Pouch Klip (150g)**: `1` pcs _(1 pcs × Rp 700 = Rp 700)_
    - **Stiker Label Kemasan Halala**: `1` pcs _(1 pcs × Rp 300 = Rp 300)_
    - **Plastik Seal Satuan (Dalam)**: `10` pcs _(10 pcs × Rp 30 = Rp 300)_
3. **Periksa Box Realtime HPP**:
    - Total Modal Bahan (HPP) = **Rp 8.345**
    - Harga Titip = **Rp 12.500**
    - Margin Keuntungan = **Rp 4.155 (33.2%)**
4. Klik **"Simpan Resep"**.

---

## 4️⃣ Langkah 4: Input Master Toko Mitra

**Menu:** `Master Toko Mitra` (`http://halala-food.test/stores`)
Tambahkan 2 toko melalui tombol **"+ Tambah Toko Mitra"**:

1. **Toko 1**:
    - **Nama Toko**: `Pusat Oleh-Oleh Barokah`
    - **Nama Pemilik**: `Ibu Hj. Aminah`
    - **No. Telepon / WhatsApp**: `081234567890`
    - **Alamat**: `Jl. Raya Pasar Besar No. 12`
2. **Toko 2**:
    - **Nama Toko**: `Toko Snack Berkah Jaya`
    - **Nama Pemilik**: `Pak Bambang`
    - **No. Telepon / WhatsApp**: `085798765432`
    - **Alamat**: `Jl. Ahmad Yani No. 45`

---

## 5️⃣ Langkah 5: Input Transaksi Kas Awal (Modal & Belanja Bahan)

**Menu:** `Buku Kas & Keuangan` (`http://halala-food.test/cash-book`)

### Transaksi 1: Setoran Modal Awal

1. Klik **"+ Pemasukan Kas"**.
2. Pilih Akun: `Kas Tunai Usaha`.
3. Kategori: ketik `Setoran Modal`.
4. Nominal: `2500000` (Rp 2.500.000).
5. Keterangan: `Setoran modal awal usaha pemilik`.
6. Klik **Simpan Transaksi**.
    - _Hasil_: Saldo Kas Usaha menjadi **Rp 2.500.000**.
    - _Jurnal Otomatis_: Dr. Kas Tunai Usaha (1-1001) Rp 2.500.000 | Cr. Modal Usaha Pemilik (3-1000) Rp 2.500.000.

### Transaksi 2: Pengeluaran Belanja Bahan Baku

1. Klik **"- Pengeluaran Kas"**.
2. Pilih Akun: `Kas Tunai Usaha`.
3. Kategori: ketik `Belanja Bahan Baku`.
4. Nominal: `1669000` (Rp 1.669.000).
5. Keterangan: `Pembelian kacang 14kg, susu 6kg, gula 8kg, mentega 2kg, 200 pouch, 200 stiker, 2000 wrap`.
6. Klik **Simpan Transaksi**.
    - _Hasil_: Saldo Kas Usaha menjadi **Rp 831.000** (Rp 2.500.000 - Rp 1.669.000).
    - _Jurnal Otomatis_: Dr. Persediaan Bahan Baku (1-1300) Rp 1.669.000 | Cr. Kas Tunai Usaha (1-1001) Rp 1.669.000.

---

## 6️⃣ Langkah 6: Catat Produksi Makanan (Batch 200 Pouch)

**Menu:** `Catat Produksi` (`http://halala-food.test/productions`)

1. Klik tombol **"+ Catat Hasil Masak / Produksi"**.
2. Pilih Produk: `Ting Ting Susu (Pouch 150g / 10 pcs)`.
3. Jumlah Diproduksi: `200` bungkus.
4. Catatan: `Produksi batch 1 perdana 200 pouch`.
5. Klik **Simpan Produksi**.

### 🔍 Cek Hasil Setelah Produksi:

- **Stok Bahan Baku** (`/raw-materials`): Seluruh stok bahan baku otomatis terpotong menjadi **0** (habis terpakai sempurna).
- **Stok Produk Jadi** (`/products`): Bertambah menjadi **200 bungkus**.
- **Jurnal Akuntansi** (`/accounting/journals`): Tercatat otomatis:
    - **Debet**: `1-1400` Persediaan Produk Jadi = **Rp 1.669.000**
    - **Kredit**: `1-1300` Persediaan Bahan Baku = **Rp 1. 669.000**

---

## 7️⃣ Langkah 7: Catat Titip Barang ke 2 Toko (Drop Konsinyasi)

**Menu:** `Titip Jual / Konsinyasi` (`http://halala-food.test/consignments`)

### Drop 1 ke Toko Barokah

1. Klik **"+ Titip Barang Baru"**.
2. Pilih Toko: `Pusat Oleh-Oleh Barokah`.
3. Pada tabel produk _Ting Ting Susu_, isi Jumlah Titip: `100` bungkus.
4. Klik **Simpan Titip Barang**.
    - _Stok gudang sisa 100 bungkus._

### Drop 2 ke Toko Berkah Jaya

1. Klik **"+ Titip Barang Baru"**.
2. Pilih Toko: `Toko Snack Berkah Jaya`.
3. Pada tabel produk _Ting Ting Susu_, isi Jumlah Titip: `100` bungkus.
4. Klik **Simpan Titip Barang**.
    - _Stok gudang menjadi 0 bungkus (seluruh 200 pouch berada di 2 toko mitra)._

---

## 8️⃣ Langkah 8: Audit Cek Sisa & Pelunasan Toko (Settlement)

**Menu:** `Titip Jual / Konsinyasi` (`http://halala-food.test/consignments`)

### A. Penagihan Toko 1 (Pusat Oleh-Oleh Barokah)

1. Klik tombol **"Cek Sisa & Tagihan"** pada nota _Pusat Oleh-Oleh Barokah_.
2. Masukkan data audit:
    - **Sisa di Display Toko**: `0`
    - **Barang Rusak / Retur**: `0`
    - **Jumlah Terjual**: Otomatis terhitung `100` bungkus.
    - **Total Tagihan**: Otomatis terhitung `Rp 1.250.000` (100 × Rp 12.500).
    - **Akun Kas Penerima**: `Kas Tunai Usaha`.
    - **Jumlah Uang Diterima**: `1250000`.
3. Klik **Simpan Hasil Tagihan & Selesaikan**.

### B. Penagihan Toko 2 (Toko Snack Berkah Jaya)

1. Klik tombol **"Cek Sisa & Tagihan"** pada nota _Toko Snack Berkah Jaya_.
2. Masukkan data audit:
    - **Sisa di Display Toko**: `0`
    - **Barang Rusak / Retur**: `0`
    - **Jumlah Terjual**: Otomatis terhitung `100` bungkus.
    - **Total Tagihan**: Otomatis terhitung `Rp 1.250.000` (100 × Rp 12.500).
    - **Akun Kas Penerima**: `Kas Tunai Usaha`.
    - **Jumlah Uang Diterima**: `1250000`.
3. Klik **Simpan Hasil Tagihan & Selesaikan**.

---

## 📊 Langkah 9: Pencocokan Hasil Akhir di Seluruh Menu Sistem

Cocokkan angka-angka berikut dengan tampilan di layar aplikasi Anda:

### 1. Menu `Buku Kas & Keuangan` (`/cash-book`)

- **Saldo Kas Tunai Usaha**: **Rp 3.331.000**
    - _Rincian:_ Modal Awal (Rp 2.500.000) - Belanja Bahan (Rp 1.669.000) + Setoran Toko 1 (Rp 1.250.000) + Setoran Toko 2 (Rp 1.250.000).

---

### 2. Menu `Jurnal Umum Akuntansi` (`/accounting/journals`)

- **Total Debet Jurnal**: **Rp 10.007.000**
- **Total Kredit Jurnal**: **Rp 10.007.000** _(Balance)_
- **Daftar 5 Entri Jurnal:**
    1. `JU-XXXXX-001` (Setoran Modal): Dr. Kas (Rp 2.500.000) \| Cr. Modal Pemilik (Rp 2.500.000)
    2. `JU-XXXXX-002` (Beli Bahan): Dr. Persediaan Bahan (Rp 1.669.000) \| Cr. Kas (Rp 1.669.000)
    3. `JU-XXXXX-003` (Produksi 200 Pouch): Dr. Persediaan Produk Jadi (Rp 1.669.000) \| Cr. Persediaan Bahan (Rp 1.669.000)
    4. `JU-XXXXX-004` (Penagihan Toko 1):
        - Dr. Kas Tunai Usaha: `Rp 1.250.000`
        - Cr. Pendapatan Penjualan Konsinyasi: `Rp 1.250.000`
        - Dr. Beban Pokok Penjualan (HPP): `Rp 834.500`
        - Cr. Persediaan Produk Jadi: `Rp 834.500`
    5. `JU-XXXXX-005` (Penagihan Toko 2):
        - Dr. Kas Tunai Usaha: `Rp 1.250.000`
        - Cr. Pendapatan Penjualan Konsinyasi: `Rp 1.250.000`
        - Dr. Beban Pokok Penjualan (HPP): `Rp 834.500`
        - Cr. Persediaan Produk Jadi: `Rp 834.500`

---

### 3. Menu `Buku Besar Akuntansi` (`/accounting/ledger`)

Periksa saldo akhir per akun:

| Kode Akun | Nama Akun                       | Total Debet (Rp) | Total Kredit (Rp) | Saldo Akhir (Rp)       |
| :-------- | :------------------------------ | :--------------- | :---------------- | :--------------------- |
| `1-1001`  | Kas Tunai Usaha                 | `5.000.000`      | `1.669.000`       | **3.331.000** (Debet)  |
| `1-1300`  | Persediaan Bahan Baku           | `1.669.000`      | `1.669.000`       | **0**                  |
| `1-1400`  | Persediaan Produk Jadi          | `1.669.000`      | `1.669.000`       | **0**                  |
| `3-1000`  | Modal Usaha Pemilik             | `0`              | `2.500.000`       | **2.500.000** (Kredit) |
| `4-1000`  | Pendapatan Penjualan Konsinyasi | `0`              | `2.500.000`       | **2.500.000** (Kredit) |
| `5-1000`  | Beban Pokok Penjualan (HPP)     | `1.669.000`      | `0`               | **1.669.000** (Debet)  |

---

### 4. Menu `Laporan Keuangan: Laba Rugi` (`/accounting/financial-statements?tab=income_statement`)

| Komponen Laba Rugi                       | Nilai (Rp)         |
| :--------------------------------------- | :----------------- |
| **1. Total Pendapatan Usaha**            | **Rp 2.500.000**   |
| • Pendapatan Penjualan Konsinyasi        | Rp 2.500.000       |
| **2. Total Beban Pokok Penjualan (HPP)** | **(Rp 1.669.000)** |
| • Beban Pokok Penjualan (HPP)            | Rp 1.669.000       |
| **LABA KOTOR (GROSS PROFIT)**            | **Rp 831.000**     |
| **3. Total Beban Operasional**           | **(Rp 0)**         |
| **LABA BERSIH USAHA (NET PROFIT)**       | **Rp 831.000**     |

---

### 5. Menu `Laporan Keuangan: Neraca Keuangan` (`/accounting/financial-statements?tab=balance_sheet`)

| ASET (HARTA USAHA)         | Nilai (Rp)       | KEWAJIBAN & EKUITAS              | Nilai (Rp)       |
| :------------------------- | :--------------- | :------------------------------- | :--------------- |
| **Kas Tunai Usaha**        | Rp 3.331.000     | **Total Kewajiban (Hutang)**     | **Rp 0**         |
| **Persediaan Bahan Baku**  | Rp 0             | **Modal Usaha Pemilik**          | Rp 2.500.000     |
| **Persediaan Produk Jadi** | Rp 0             | **Laba Bersih Periode Berjalan** | Rp 831.000       |
|                            |                  | **Total Ekuitas / Modal**        | **Rp 3.331.000** |
| **TOTAL ASET**             | **Rp 3.331.000** | **TOTAL KEWAJIBAN & EKUITAS**    | **Rp 3.331.000** |

> ✅ **Hasil Status Neraca: 100% BALANCE / SEIMBANG** (Total Aset = Total Kewajiban + Total Ekuitas = Rp 3.331.000).
