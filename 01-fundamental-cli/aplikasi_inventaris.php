<?php
declare(strict_types=1);

const namaAplikasi = "HummaTech v1.0;";

//data awal disimpan dalam array asosiatif multidimensi
$daftarBarang = [
    [
        'kode'  => 'BRG-01',
        'nama'  => 'Laptop Dell Latitude',
        'stok'  => 10,
        'harga' => 8500000,
    ],
    [
        'kode'  => 'BRG-02',
        'nama'  => 'Monitor LG 24 Inch',
        'stok'  => 5,
        'harga' => 1850000,
    ],
];

//menampilkan menu utama
function cetakMenu(): void
{
    echo "\n=============================================\n";
    echo "   " . namaAplikasi . "\n";
    echo "=============================================\n";
    echo "[1] Tampilkan Semua Data Barang\n";
    echo "[2] Tambah Barang Baru\n";
    echo "[3] Tambah Stok Barang (Barang Masuk)\n";
    echo "[4] Kurangi Stok Barang (Barang Keluar)\n";
    echo "[0] Keluar Aplikasi\n";
    echo "---------------------------------------------\n";
}

//membaca input string dari terminal
function inputWajibTeks(string $pertanyaan): string {
    $nilai = trim((string) readline($pertanyaan));
    if ($nilai === '') {
        throw new InvalidArgumentException("Input teks tidak boleh kosong!");
    }
    return $nilai;
}

//membaca input angka bulat positif dari terminal
function inputAngkaPositif(string $pertanyaan): int {
    $nilai = trim((string) readline($pertanyaan));
    if (!ctype_digit($nilai)) {
        throw new InvalidArgumentException("Input '$nilai' tidak valid! Harus berupa angka bulat positif.");
    }
    return (int) $nilai;
}

//mencari indeks barang berdasarkan kode
function cariIndeksBarang(array $data, string $kode): ?int {
    foreach ($data as $indeks => $barang) {
        if ($barang['kode'] === $kode) {
            return $indeks;
        }
    }
    return null;
}

//menu 1: daftar seluruh barang
function lihatDaftarBarang(array $data): void {
    echo "\n--- DAFTAR INVENTARIS GUDANG ---\n";
    if (count($data) === 0) {
        echo "Data inventaris masih kosong.\n";
        return;
    }

    $totalAset = 0;
    foreach ($data as $i => $barang) {
        $nomor = $i + 1;
        $subtotal = $barang['stok'] * $barang['harga'];
        $totalAset += $subtotal;

        $hargaStr = number_format($barang['harga'], 0, ',', '.');
        echo "{$nomor}. [{$barang['kode']}] {$barang['nama']} | Stok: {$barang['stok']} unit | Harga: Rp {$hargaStr}\n";
    }
    echo "---------------------------------------------\n";
    echo "Total Nilai Aset Gudang: Rp " . number_format($totalAset, 0, ',', '.') . "\n";
}

//menu 2: item barang baru
function tambahBarangBaru(array &$data): void
{
    echo "\n--- TAMBAH BARANG BARU ---\n";
    $kode = strtoupper(inputWajibTeks("Masukkan Kode Barang (contoh BRG-03): "));

    if (cariIndeksBarang($data, $kode) !== null) {
        throw new RuntimeException("Kode barang '$kode' sudah digunakan oleh barang lain!");
    }

    $nama  = inputWajibTeks("Masukkan Nama Barang: ");
    $stok  = inputAngkaPositif("Masukkan Stok Awal: ");
    $harga = inputAngkaPositif("Masukkan Harga Satuan (Rp): ");

    $data[] = [
        'kode'  => $kode,
        'nama'  => $nama,
        'stok'  => $stok,
        'harga' => $harga,
    ];

    echo "[BERHASIL] Barang '$nama' ($kode) telah ditambahkan ke sistem.\n";
}

//menu 3: jumlah stok
function updateStokBarang(array &$data, string $tipeTransaksi): void
{
    lihatDaftarBarang($data);
    $judul = ($tipeTransaksi === 'MASUK') ? "BARANG MASUK (+)" : "BARANG KELUAR (-)";
    echo "\n--- TRANSAKSI $judul ---\n";

    $kode = strtoupper(inputWajibTeks("Masukkan Kode Barang: "));
    $indeks = cariIndeksBarang($data, $kode);

    if ($indeks === null) {
        throw new RuntimeException("Barang dengan kode '$kode' tidak ditemukan!");
    }

    $jumlah = inputAngkaPositif("Masukkan Jumlah Unit: ");
    if ($jumlah === 0) {
        throw new InvalidArgumentException("Jumlah unit harus lebih dari 0.");
    }

    if ($tipeTransaksi === 'MASUK') {
        $data[$indeks]['stok'] += $jumlah;
        echo "[BERHASIL] Stok '{$data[$indeks]['nama']}' bertambah +$jumlah. Total stok: {$data[$indeks]['stok']}.\n";
    } else {
        if ($jumlah > $data[$indeks]['stok']) {
            throw new RuntimeException("Stok tidak mencukupi! Sisa stok saat ini: {$data[$indeks]['stok']} unit.");
        }
        $data[$indeks]['stok'] -= $jumlah;
        echo "[BERHASIL] Stok '{$data[$indeks]['nama']}' berkurang -$jumlah. Sisa stok: {$data[$indeks]['stok']}.\n";
    }
}


//MAIN LOOP
while (true) {
    cetakMenu();
    $menu = trim((string) readline("Pilih menu (0-4): "));

    try {
        match ($menu) {
            '1' => lihatDaftarBarang($daftarBarang),
            '2' => tambahBarangBaru($daftarBarang),
            '3' => updateStokBarang($daftarBarang, 'MASUK'),
            '4' => updateStokBarang($daftarBarang, 'KELUAR'),
            '0' => (function () {
                echo "Menutup aplikasi. Sampai jumpa!\n";
                exit(0);
            })(),
            default => throw new InvalidArgumentException("Pilihan menu '$menu' tidak valid. Pilih angka 0 sampai 4."),
        };
    } catch (InvalidArgumentException | RuntimeException $e) {
        // Error validasi pengguna atau aturan bisnis ditangani dengan anggun
        echo "\n[PERINGATAN] " . $e->getMessage() . "\n";
    } catch (Throwable $e) {
        // Pengaman terakhir jika terjadi error sistem
        echo "\n[ERROR SISTEM] " . $e->getMessage() . "\n";
    }
}