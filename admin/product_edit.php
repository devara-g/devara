<?php
require_once '../config/database.php';

$pageTitle = 'Admin - Edit Produk';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $stock = (int)$_POST['stock'];
    
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("ssdsii", $name, $description, $price, $category, $stock, $productId);
    
    if ($stmt->execute()) {
        $successMessage = 'Produk berhasil diperbarui!';
        // Refresh product data
        $product['name'] = $name;
        $product['description'] = $description;
        $product['price'] = $price;
        $product['category'] = $category;
        $product['stock'] = $stock;
    } else {
        $errorMessage = 'Gagal memperbarui produk.';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Cafe - <?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-mug-hot"></i> Warung Cafe</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/admin/">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/products.php" style="color: var(--secondary-color);">Produk</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/orders.php">Pesanan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php" target="_blank">Lihat Website</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container" style="padding: 2rem 0;">
            <div style="margin-bottom: 2rem;">
                <a href="products.php" class="btn"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
            
            <h2 style="margin-bottom: 2rem; color: var(--accent-color);">Edit Produk</h2>
            
            <?php if (isset($successMessage)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
            </div>
            <?php endif; ?>
            
            <div class="product-card" style="padding: 1.5rem; max-width: 600px;">
                <form method="POST" action="product_edit.php?id=<?php echo $productId; ?>">
                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="price">Harga (Rp)</label>
                            <input type="number" id="price" name="price" min="0" step="100" value="<?php echo $product['price']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock">Stok</label>
                            <input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="update_product" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 Warung Cafe Admin. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
