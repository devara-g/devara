<?php
require_once '../config/database.php';

$pageTitle = 'Admin - Kelola Produk';

// Handle add product
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $stock = (int)$_POST['stock'];
    
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $name, $description, $price, $category, $stock);
    
    if ($stmt->execute()) {
        $successMessage = 'Produk berhasil ditambahkan!';
    } else {
        $errorMessage = 'Gagal menambahkan produk.';
    }
    $stmt->close();
}

// Handle delete product
if (isset($_GET['delete'])) {
    $productId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    if ($stmt->execute()) {
        $successMessage = 'Produk berhasil dihapus!';
    }
    $stmt->close();
    header('Location: products.php');
    exit;
}

// Get all products
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
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
            <h2 style="margin-bottom: 2rem; color: var(--accent-color);">Kelola Produk</h2>
            
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
            
            <!-- Add Product Form -->
            <div class="product-card" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--accent-color);">Tambah Produk Baru</h3>
                
                <form method="POST" action="products.php">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="name">Nama Produk</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Kategori</label>
                            <input type="text" id="category" name="category" placeholder="Contoh: Minuman, Makanan" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="price">Harga (Rp)</label>
                            <input type="number" id="price" name="price" min="0" step="100" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock">Stok</label>
                            <input type="number" id="stock" name="stock" min="0" value="100" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_product" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                </form>
            </div>
            
            <!-- Products Table -->
            <div class="product-card" style="padding: 1.5rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--accent-color);">Daftar Produk</h3>
                
                <?php if (!empty($products)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($product['stock'] > 10): ?>
                                <span style="color: var(--success-color);"><?php echo $product['stock']; ?></span>
                                <?php elseif ($product['stock'] > 0): ?>
                                <span style="color: #ffc107;"><?php echo $product['stock']; ?></span>
                                <?php else: ?>
                                <span style="color: var(--danger-color);">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn" style="padding: 5px 10px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-danger" style="padding: 5px 10px;" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; color: #666;">Belum ada produk.</p>
                <?php endif; ?>
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
