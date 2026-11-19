<?php 
declare(strict_types=1);

$katalog = [
    'HT-001' => [
        'nama' => 'Laptop ASUS',
        'kategori' => 'Elektronik',
        'harga' => 5000000,
        'stok' => 3,
    ],
    'HT-002' => [
        'nama' => 'Es Teh',
        'kategori' => 'Minuman',
        'harga' => 3000,
        'stok' => 10,
    ],
    'HT-003' => [
        'nama' => 'Nasi goreng',
        'kategori' => 'Makanan',
        'harga' => 15000,
        'stok' => 5,
    ],
];

$keranjang = [];

function tambahKeranjang(array &$keranjang, array $katalog, string $kode, int $kuantitas): void
{
    $kode = strtoupper(trim($kode));

    if (!isset($katalog[$kode])) {
        throw new InvalidArgumentException("Katalog dengan kode '$kode' tidak ditemukan di katalog.");
    }

    if ($kuantitas <= 0) {
        throw new InvalidArgumentException("Jumlah pembelian minimal adalah 1 unit.");
    }

    $produk = $katalog[$kode];
    $kuantitasSaatIni = $keranjang[$kode]['kuantitas'] ?? 0;
    $totalKuantitasBaru = $kuantitasSaatIni + $kuantitas;

    if ($totalKuantitasBaru > $produk['stok']) {
        throw new RuntimeException(
            "Stok '{$produk['nama']}' tidak mencukupi! Tersedia: {$produk['stok']}, diminta: {$totalKuantitasBaru}."
        );
    }

    if (isset($keranjang[$kode])) {
        $keranjang[$kode]['kuantitas'] = $totalKuantitasBaru;
        return;
    }

    $keranjang[$kode] = [
        'kode' => $kode,
        'nama' => $produk['nama'],
        'kategori' => $produk['kategori'],
        'harga' => $produk['harga'],
        'kuantitas' => $kuantitas,
    ];
}

function hitungSubTotalKotor(array $keranjang): float
{
    $total = 0.0;
    foreach ($keranjang as $item) {
        $total += ($item['harga'] * $item['kuantitas']);
    }
    return $total;
}

function hitungSubTotalKategori(array $keranjang, string $targetKategori): float
{
    $totalKategori = 0.0;
    foreach ($keranjang as $item) {
        if (strcasecmp($item['kategori'], $targetKategori) === 0) {
            $totalKategori += ($item['harga'] * $item['kuantitas']);
        }
    }
    return $totalKategori;
}

function hitungDiskonElektronik(array $keranjang): float
{
    $subtotalElektronik = hitungSubTotalKategori($keranjang, 'Elektronik');
    if ($subtotalElektronik > 5000000) {
        return $subtotalElektronik * 0.10;
    }
    return 0.0;
}

function hitungOngkirAkhir(float $subtotalKotor): float
{
    if ($subtotalKotor <= 0) {
        return 0.0;
    }

    $ongkirAwal = 30000.0;
    $potongOngkir = ($subtotalKotor >= 1000000) ? 25000.0 : 0.0;

    return $ongkirAwal - $potongOngkir;
}

function hitungDiskonPromo(float $subtotalKotor, ?string $kodePromo): float
{
    if ($kodePromo === null || trim($kodePromo) === '') {
        return 0.0;
    }

    $kodePromo = strtoupper(trim($kodePromo));

    if ($kodePromo !== 'HUMMATECH50') {
        throw new InvalidArgumentException("Kode promo '{$kodePromo}' tidak valid atau sudah kedaluwarsa.");
    }

    if ($subtotalKotor < 300000) {
        throw new RuntimeException(
            "Kode promo '{$kodePromo}' membutuhkan minimal belanja Rp 300.000."
        );
    }

    return 50000.0;
}

function hitungCheckout(array $keranjang, ?string $kodePromo = null): array
{
    if (empty($keranjang)) {
        throw new RuntimeException("Keranjang belanja masih kosong! Tidak dapat memproses checkout.");
    }

    $subtotalKotor    = hitungSubTotalKotor($keranjang);
    $diskonElektronik = hitungDiskonElektronik($keranjang);
    $ongkirAkhir      = hitungOngkirAkhir($subtotalKotor);
    $diskonPromo      = hitungDiskonPromo($subtotalKotor, $kodePromo);

    $grandTotal = max(0.0, ($subtotalKotor - $diskonElektronik - $diskonPromo) + $ongkirAkhir);

    return [
        'subtotal_kotor'    => $subtotalKotor,
        'diskon_elektronik' => $diskonElektronik,
        'ongkir_akhir'      => $ongkirAkhir,
        'diskon_promo'      => $diskonPromo,
        'grand_total'       => $grandTotal,
    ];
}

function cetakStruk(array $keranjang, array $rincian): void
{
    echo "\n==================================================\n";
    echo "              STRUK PEMBAYARAN KASIR              \n";
    echo "==================================================\n";
    foreach ($keranjang as $item) {
        $subtotalItem = $item['harga'] * $item['kuantitas'];
        echo sprintf(
            "%-22s x%-2d : Rp %12s\n",
            $item['nama'],
            $item['kuantitas'],
            number_format($subtotalItem, 0, ',', '.')
        );
    }
    echo "--------------------------------------------------\n";
    echo sprintf("Subtotal Kotor            : Rp %12s\n", number_format($rincian['subtotal_kotor'], 0, ',', '.'));
    echo sprintf("Diskon Elektronik (10%%)   : -Rp %11s\n", number_format($rincian['diskon_elektronik'], 0, ',', '.'));
    echo sprintf("Diskon Kode Promo         : -Rp %11s\n", number_format($rincian['diskon_promo'], 0, ',', '.'));
    echo sprintf("Ongkos Kirim              : +Rp %11s\n", number_format($rincian['ongkir_akhir'], 0, ',', '.'));
    echo "==================================================\n";
    echo sprintf("GRAND TOTAL BAYAR         : Rp %12s\n", number_format($rincian['grand_total'], 0, ',', '.'));
    echo "==================================================\n";
}

echo ">>> TEST CASE 1: Transaksi Normal & Penggabungan Item Keranjang <<<\n";
try {
    tambahKeranjang($keranjang, $katalog, 'HT-001', 1); 
    tambahKeranjang($keranjang, $katalog, 'HT-001', 1);
    tambahKeranjang($keranjang, $katalog, 'HT-003', 2);

    $hasil = hitungCheckout($keranjang, 'HUMMATECH50');
    cetakStruk($keranjang, $hasil);
} catch (Throwable $e) {
    echo "[ERROR TEST 1]: " . $e->getMessage() . "\n";
}

echo "\n>>> TEST CASE 2: Uji Validasi Stok Berlebih <<<\n";
try {
    tambahKeranjang($keranjang, $katalog, 'HT-001', 4);
} catch (Throwable $e) {
    echo "[TERTANGKAP SESUAI HARAPAN]: " . $e->getMessage() . "\n";
}

echo "\n>>> TEST CASE 3: Uji Syarat Minimal Kode Promo <<<\n";
try {
    $keranjangKecil = [];
    tambahKeranjang($keranjangKecil, $katalog, 'HT-003', 2);
    hitungCheckout($keranjangKecil, 'HUMMATECH50');
} catch (Throwable $e) {
    echo "[TERTANGKAP SESUAI HARAPAN]: " . $e->getMessage() . "\n";
}