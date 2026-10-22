# Pengukuran Memori: get() vs lazyById()

## Tujuan
Membandingkan penggunaan memori antara metode get() dan lazyById() pada tabel transaksi.

## Hasil Pengukuran

Pengukuran dilakukan terhadap 52.500 data transaksi.

| Metode | Jumlah Data | Peak Memory |
|---|---:|---:|
| Transaction::lazyById() | 52.500 | 70,1 MB |
| Transaction::get() | 52.500 | 128,4 MB |

## Analisis
Berdasarkan hasil pengukuran, lazyById() menggunakan peak memory sebesar 70,1 MB, sedangkan get() menggunakan 128,4 MB. Selisih penggunaan memori keduanya adalah 58,3 MB.

Pada pengujian ini, lazyById() menggunakan memori lebih sedikit karena data dibaca secara bertahap, sedangkan get() mengambil seluruh data sekaligus.

## Kesimpulan
Berdasarkan hasil pengujian ini, lazyById() lebih hemat memori dibandingkan get() untuk membaca 52.500 data transaksi. Hasil pengukuran dapat berbeda pada lingkungan lain.
