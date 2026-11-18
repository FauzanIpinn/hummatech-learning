USE hummatech_inventory;

-- INSERT DATA ke Tabel Users
INSERT INTO users (name, email, role) VALUES 
('Ahmad Fauzan', 'ahmad@hummatech.co.id', 'admin'),
('Budi Santoso', 'budi@hummatech.co.id', 'staff');

INSERT INTO categories (name, description) VALUES
('Perangkat Jaringan', 'Router, Switch, Access Point, dan Kabel LAN'),
('Komputer & Laptop', 'Unit PC, Laptop operasional, dan Monitor'),
('Aksesoris IT', 'Keyboard, Mouse, dan periferal pendukung');

INSERT INTO products (category_id, sku, name, stock, price) VALUES
(1, 'NET-001', 'Router Mikrotik RB750Gr3', 15, 950000.00),
(1, 'NET-002', 'Kabel LAN Belden Cat6 (Box)', 8, 1350000.00),
(2, 'CMP-001', 'Laptop Lenovo ThinkPad T14', 6, 14500000.00),
(2, 'CMP-002', 'Monitor Dell 24 Inch IPS', 10, 2400000.00);

INSERT INTO stock_transactions (product_id, user_id, type, quantity, notes) VALUES
(1, 1, 'IN', 15, 'Stok awal pengadaan gudang'),
(1, 2, 'OUT', 3, 'Pengiriman ke divisi infrastruktur'),
(3, 1, 'OUT', 1, 'Unit kerja developer baru');

-- READ (SELECT & FILTERING)
SELECT * FROM products; --Menampilkan semua produk

SELECT sku, name, stock, price FROM products WHERE stock < 10 ORDER BY stock ASC; --Menampilkan produk dibawah 10 unit

-- UPDATE Data
UPDATE products SET stock = stock - 3, price = 975000.00 WHERE sku = 'NET-001'; --Menyertakan WHERE agar tidak mengubah seluruh baris tabel

SELECT sku, name, stock, price FROM products WHERE sku = 'NET-001'; --Mengecek hasil perubahan

-- DELETE
DELETE FROM stock_transactions WHERE id = 3;