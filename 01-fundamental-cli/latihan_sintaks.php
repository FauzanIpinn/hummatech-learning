<?php
declare(strict_types=1);

echo "1. VARIABEL, KONSTANTA, TIPE DATA, OPERATOR\n";

// Konstanta (Nilai tetap)
define('PERUSAHAAN', 'PT. Humma Teknologi Indonesia');
const PAJAK_PPN = 0.11;

// Variabel & Tipe Data
$namaProduk  = "Server Rack 42U";
$jumlahUnit  = 2;
$hargaSatuan = 8500000.0;
$isReady     = true;
$gudang      = ["Malang", "Surabaya"];

// Operator Aritmatika & Logika
$subtotal   = $jumlahUnit * $hargaSatuan;
$nilaiPajak = $subtotal * PAJAK_PPN;
$grandTotal = $subtotal + $nilaiPajak;
$statusJual = ($isReady && $jumlahUnit > 0) ? "Dapat Dijual" : "Kosong";

echo "Instansi    : " . PERUSAHAAN . "\n";
echo "Produk      : $namaProduk ($statusJual)\n";
echo "Lokasi      : " . $gudang[0] . "\n";
echo "Grand Total : Rp " . number_format($grandTotal, 0, ',', '.') . "\n\n";


echo "2. PERCABANGAN (IF-ELSE & MATCH)\n";

$skorEvaluasi = 85;

if ($skorEvaluasi >= 90) {
    $predikat = "Sangat Memuaskan";
} elseif ($skorEvaluasi >= 75) {
    $predikat = "Memenuhi Target";
} else {
    $predikat = "Memerlukan Perbaikan";
}

// Match expression (PHP 8+)
$kodeStatus = 201;
$pesanHttp = match ($kodeStatus) {
    200 => "OK - Permintaan Berhasil",
    201 => "Created - Data Berhasil Dibuat",
    400 => "Bad Request - Input Tidak Valid",
    404 => "Not Found - Data Tidak Ditemukan",
    default => "Status Tidak Dikenal",
};

echo "Hasil Evaluasi : $predikat (Skor: $skorEvaluasi)\n";
echo "Simulasi HTTP  : [$kodeStatus] $pesanHttp\n\n";


echo "3. PERULANGAN (FOR, WHILE, FOREACH)\n";

// Perulangan For
echo "Hitung mundur server restart: ";
for ($i = 3; $i >= 1; $i--) {
    echo "$i... ";
}
echo "Selesai!\n";

// Perulangan Foreach (Paling krusial di Backend)
$timBackend = [
    ['id' => 101, 'nama' => 'Ahmad', 'role' => 'Intern Backend'],
    ['id' => 102, 'nama' => 'Budi',  'role' => 'Team Leader'],
];

foreach ($timBackend as $anggota) {
    echo "- ID #{$anggota['id']}: {$anggota['nama']} ({$anggota['role']})\n";
}
echo "\n";


echo "4. FUNCTION, PARAMETER & RETURN TYPE\n";

function hitungGajiBersih(float $gajiPokok, float $tunjangan = 0.0, float $potongan = 0.0): float
{
    return ($gajiPokok + $tunjangan) - $potongan;
}

$gaji = hitungGajiBersih(4500000, 500000, 150000);
echo "Gaji Bersih: Rp " . number_format($gaji, 0, ',', '.') . "\n\n";


echo "5. ERROR HANDLING DASAR (TRY-CATCH)\n";

function bagiAngka(float $pembilang, float $penyebut): float
{
    if ($penyebut === 0.0) {
        throw new DivisionByZeroError("Pembagi tidak boleh bernilai nol (0)!");
    }
    return $pembilang / $penyebut;
}

try {
    echo "Hasil 10 / 2 = " . bagiAngka(10, 2) . "\n";
    echo "Hasil 10 / 0 = " . bagiAngka(10, 0) . "\n";
} catch (DivisionByZeroError $e) {
    echo "[TERTANGKAP ERROR]: " . $e->getMessage() . "\n";
} finally {
    echo "Blok finally selalu dieksekusi baik sukses maupun error.\n";
}