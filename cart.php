<?php
require_once 'config/database.php';

$pageTitle = 'Keranjang Belanja';

// Handle quantity update
if (isset($_POST['update_quantity'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        $_SESSION['cart'][$productId] = $quantity;
    } else {
        removeFromCart($productId);
    }

    header('Location: cart.php?success=keranjang_diperbarui');
    exit;
}

// Handle remove item
if (isset($_GET['remove'])) {
    $productId = (int)$_GET['remove'];
    removeFromCart($productId);
    header('Location: cart.php?success=item_dihapus');
    exit;
}

// Get cart items
$cartItems = getCartItems($conn);
$cartTotal = getCartTotal($conn);
?>

<?php include 'components/header.php'; ?>

<section>
    <div class="container">
        <div class="section-title">
            <h2>Keranjang Belanja</h2>
            <p>Review物品 sebelum checkout</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php
                $successMsg = $_GET['success'];
                if ($successMsg === 'ditambahkan_ke_keranjang') echo 'Produk berhasil ditambahkan ke keranjang!';
                elseif ($successMsg === 'keranjang_diperbarui') echo 'Keranjang berhasil diperbarui!';
                elseif ($successMsg === 'item_dihapus') echo 'Item berhasil dihapus dari keranjang!';
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($cartItems)): ?>
            <div class="cart-container">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumb">
                                            <i class="fas fa-coffee"></i>
                                        </div>
                                        <div>
                                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($item['category']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <form method="POST" action="cart.php" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="quantity-input">
                                        <button type="submit" name="update_quantity" class="btn" style="padding: 5px 10px; margin-left: 5px;">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus item ini dari keranjang?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp <?php echo number_format($cartTotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Pajak (10%)</span>
                        <span>Rp <?php echo number_format($cartTotal * 0.1, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span>Rp <?php echo number_format($cartTotal * 1.1, 0, ',', '.'); ?></span>
                    </div>

                    <div style="margin-top: 2rem;">
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; text-align: center;">
                            <i class="fas fa-credit-card"></i> Lanjut ke Checkout
                        </a>
                        <a href="products.php" class="btn" style="width: 100%; text-align: center; margin-top: 1rem;">
                            <i class="fas fa-shopping-bag"></i> Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>Keranjang Kosong</h3>
                <p>Anda belum menambahkan apapun ke keranjang.</p>
                <a href="products.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'components/footer.php'; ?>