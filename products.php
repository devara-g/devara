<?php
require_once 'config/database.php';

$pageTitle = 'Menu';

// Get filter category from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Get categories
$categories = [];
$result = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Get products with optional category filter
$products = [];
if ($category && $category !== 'all') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY name");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM products ORDER BY name");
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<?php include 'components/header.php'; ?>

<section>
    <div class="container">
        <div class="section-title">
            <h2>Menu Kami</h2>
            <p>Pilih favorit Anda</p>
        </div>
        
        <!-- Category Filter -->
        <div class="category-filter">
            <a href="products.php" class="btn <?php echo ($category === '' || $category === 'all') ? 'active' : ''; ?>">Semua</a>
            <?php foreach ($categories as $cat): ?>
            <a href="products.php?category=<?php echo urlencode($cat); ?>" class="btn <?php echo ($category === $cat) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
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
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-coffee"></i>
            <h3>Produk Tidak Ditemukan</h3>
            <p>Tidak ada produk dalam kategori ini.</p>
            <a href="products.php" class="btn btn-primary">Lihat Semua Produk</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'components/footer.php'; ?>
