# Mengapa Total Transaksi Harus Dihitung di Server?

## 1. Validasi Angka Saja Tidak Cukup

Validasi angka hanya memastikan bahwa nilai yang dimasukkan memiliki format yang sesuai. Namun, validasi tersebut tidak menjamin bahwa nilai total transaksi benar. Pengguna dapat mengubah data yang dikirim melalui browser menggunakan Developer Tools atau mengirim permintaan secara langsung menggunakan cURL.

## 2. Risiko Jika Total Dipercaya dari Pengguna

Jika aplikasi menerima total transaksi langsung dari pengguna, nilai tersebut dapat dimanipulasi. Contohnya, sebuah produk memiliki harga Rp100.000, tetapi pengguna mengubah nilai total yang dikirim menjadi Rp1. Jika server menerima nilai tersebut tanpa menghitung ulang, transaksi dapat tersimpan dengan jumlah pembayaran yang tidak sesuai.

## 3. Cara Menghitung Total yang Benar

Total transaksi harus dihitung di server berdasarkan harga produk yang tersimpan di database dan jumlah produk yang dibeli. Server tidak boleh langsung mempercayai harga atau total yang dikirim oleh browser.

Rumus perhitungan:

**Total transaksi = jumlah seluruh (harga produk × kuantitas)**

Dengan cara ini, total dihitung berdasarkan data yang tersimpan di server sehingga lebih aman dan akurat.

## 4. Kesimpulan

Total transaksi harus dihitung di server karena data dari pengguna tidak selalu dapat dipercaya. Validasi format angka saja tidak cukup untuk mencegah manipulasi nilai transaksi. Dengan mengambil harga produk dari database dan menghitung ulang total di server, aplikasi dapat mengurangi risiko transaksi dengan nilai yang tidak sesuai.