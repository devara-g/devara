<?php
require_once '../config/database.php';

$pageTitle = 'Admin - Detail Pesanan';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: orders.php');
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

// Get order items
$orderItems = [];
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderItems[] = $row;
    }
}
$stmt->close();

// Handle update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    if ($stmt->execute()) {
        $successMessage = 'Status pesanan berhasil diperbarui!';
        $order['status'] = $status;
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
                    <li><a href="<?php echo BASE_URL; ?>/admin/products.php">Produk</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/orders.php" style="color: var(--secondary-color);">Pesanan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php" target="_blank">Lihat Website</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container" style="padding: 2rem 0;">
            <div style="margin-bottom: 2rem;">
                <a href="orders.php" class="btn"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>

            <h2 style="margin-bottom: 2rem; color: var(--accent-color);">Detail Pesanan #<?php echo $orderId; ?></h2>

            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Customer Info -->
                <div class="product-card" style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--accent-color);">Informasi Pelanggan</h3>

                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><strong>Telepon:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($order['customer_address']); ?></p>
                </div>

                <!-- Order Status -->
                <div class="product-card" style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--accent-color);">Status Pesanan</h3>

                    <form method="POST" action="order_detail.php?id=<?php echo $orderId; ?>">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="pending" <?php echo ($order['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo ($order['status'] === 'processing') ? 'selected' : ''; ?>>Diproses</option>
                                <option value="shipped" <?php echo ($order['status'] === 'shipped') ? 'selected' : ''; ?>>Dikirim</option>
                                <option value="delivered" <?php echo ($order['status'] === 'delivered') ? 'selected' : ''; ?>>Diterima</option>
                                <option value="cancelled" <?php echo ($order['status'] === 'cancelled') ? 'selected' : ''; ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <button type="submit" name="update_status" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Status
                        </button>
                    </form>

                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
                        <p><strong>Tanggal Pesanan:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="product-card" style="padding: 1.5rem; margin-top: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--accent-color);">Item Pesanan</h3>

                <?php if (!empty($orderItems)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: 700;">Total:</td>
                                <td style="font-weight: 700; color: var(--primary-color);">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666;">Tidak ada item pesanan.</p>
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