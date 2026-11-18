-- INNER JOIN (Menggabungkan 2 tabel: products + categories)
SELECT 
    p.id AS product_id,
    p.sku, 
    p.name AS product_name, 
    p.stock, 
    p.price 
FROM products p INNER JOIN categories c ON category_id = c.id; -- Menampilkan kategori yang memiliki produk

-- LEFT JOIN (Menampilkan semua kategori termasuk yang kosong)
SELECT 
    c.name AS category_name,
    p.sku,
    p.name AS product_name,
    p.stock
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
ORDER BY c.id ASC;

-- MULTI TABLE JOIN (Menggabungkan 3 tabel: Transaksi + Produk + User)
SELECT
    st.id AS trx_id,
    st.transaction_daye,
    st.type AS jenis_transaksi,
    p.sku,
    p.name AS nama_barang,
    st.quantity AS jumlah,
    u.name AS penanggung_jawab,
    st.notes
FROM stock_transactions st
INNER JOIN products p ON st.product_id = p.id
INNER JOIN users u ON st.user_id = u.id
ORDER BY st.transaction_date DESC;

-- AGREGASI & GROUP BY (Ringkasan Stok per Kategori)
SELECT
    c.name AS kategori,
    COUNT(p.id) AS jumlah_jenis_barang,
    COALESCE(SUM(p.stock), 0) AS total_unit_stok
FROM categories categories
LEFT JOIN products p ON c.id = p.category_id
GROUP BY c.id, c.name;