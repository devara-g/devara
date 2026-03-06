<?php
require_once 'config/database.php';

$pageTitle = 'Beranda';

// Get featured products (first 6)
$featuredProducts = [];
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $featuredProducts[] = $row;
    }
}

// Get categories
$categories = [];
$result = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}
?>

<?php include 'components/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h2>Selamat Datang di Warung Cafe</h2>
        <p>Nikmati kopi dan makanan terbaik dengan harga terjangkau</p>
        <a href="products.php" class="btn">Lihat Menu</a>
    </div>
</section>

<!-- Products Section -->
<section>
    <div class="container">
        <div class="section-title">
            <h2>Menu Favorit</h2>
            <p>Pilihan terbaik dari kami untuk Anda</p>
        </div>

        <?php if (!empty($featuredProducts)): ?>
            <div class="product-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <p class="stock">Stok: <?php echo $product['stock']; ?></p>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">Detail</a>
                                <?php if ($product['stock'] > 0): ?>
                                    <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-cart-plus"></i> Tambah
                                    </a>
                                <?php else: ?>
                                    <button class="btn" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="products.php" class="btn btn-primary">Lihat Semua Menu</a>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-coffee"></i>
                <h3>Belum Ada Produk</h3>
                <p>Silakan hubungi admin untuk menambahkan produk.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- About Section -->
<section style="background: white;">
    <div class="container">
        <div class="section-title">
            <h2>Tentang Kami</h2>
            <p>Warung Cafe - Tempatnya Kopi dan Makanan Lezat</p>
        </div>
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <p style="font-size: 1.1rem; line-height: 1.8; color: #666;">
                Warung Cafe adalah tempat yang sempurna untuk menikmati kopi berkualitas tinggi dan makanan lezat.
                Kami berkomitmen untuk menyediakan produk terbaik dengan harga yang terjangkau bagi semua pelanggan kami.
                Dengan suasana yang nyaman dan pelayanan yang ramah, kami siap memberikan pengalaman terbaik untuk Anda.
            </p>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>