<?php
require_once 'config/database.php';

$pageTitle = 'Detail Produk';

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId === 0) {
    header('Location: products.php');
    exit;
}

// Get product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: products.php');
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Get related products (same category)
$relatedProducts = [];
$stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$stmt->bind_param("si", $product['category'], $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $relatedProducts[] = $row;
    }
}
$stmt->close();
?>

<?php include 'components/header.php'; ?>

<section>
    <div class="container">
        <div style="margin-bottom: 2rem;">
            <a href="products.php" class="btn"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="product-detail-container">
            <div class="product-detail-image">
                <i class="fas fa-coffee"></i>
            </div>

            <div class="product-detail-info">
                <p style="color: #666; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($product['category']); ?></p>
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                <p class="stock">
                    <i class="fas fa-check-circle"></i> Stok Tersedia: <?php echo $product['stock']; ?>
                </p>
                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>

                <form action="add_to_cart.php" method="GET">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                    <div class="quantity-selector">
                        <label for="qty">Jumlah:</label>
                        <input type="number" id="qty" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>

                    <?php if ($product['stock'] > 0): ?>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                    <?php else: ?>
                        <button class="btn" disabled style="width: 100%;">Stok Habis</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div style="margin-top: 4rem;">
                <div class="section-title">
                    <h2>Produk Terkait</h2>
                    <p>Produk lain dalam kategori yang sama</p>
                </div>

                <div class="product-grid">
                    <?php foreach ($relatedProducts as $related): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($related['name']); ?></h3>
                                <p class="description"><?php echo htmlspecialchars($related['description']); ?></p>
                                <p class="price">Rp <?php echo number_format($related['price'], 0, ',', '.'); ?></p>
                                <div class="product-actions">
                                    <a href="product.php?id=<?php echo $related['id']; ?>" class="btn">Detail</a>
                                    <?php if ($related['stock'] > 0): ?>
                                        <a href="add_to_cart.php?id=<?php echo $related['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'components/footer.php'; ?>