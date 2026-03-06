<?php
require_once '../config/database.php';

$pageTitle = 'Admin - Kelola Pesanan';

// Handle update order status
if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    if ($stmt->execute()) {
        $successMessage = 'Status pesanan berhasil diperbarui!';
    }
    $stmt->close();
}

// Get filter status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Get orders
$orders = [];
$query = "SELECT * FROM orders";
if ($statusFilter && $statusFilter !== 'all') {
    $query .= " WHERE status = ?";
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $statusFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query .= " ORDER BY created_at DESC";
    $result = $conn->query($query);
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

if (isset($stmt)) {
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
            <h2 style="margin-bottom: 2rem; color: var(--accent-color);">Kelola Pesanan</h2>
            
            <?php if (isset($successMessage)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
            </div>
            <?php endif; ?>
            
            <!-- Status Filter -->
            <div class="category-filter" style="margin-bottom: 2rem;">
                <a href="orders.php" class="btn <?php echo ($statusFilter === '' || $statusFilter === 'all') ? 'active' : ''; ?>">Semua</a>
                <a href="orders.php?status=pending" class="btn <?php echo ($statusFilter === 'pending') ? 'active' : ''; ?>">Pending</a>
                <a href="orders.php?status=processing" class="btn <?php echo ($statusFilter === 'processing') ? 'active' : ''; ?>">Diproses</a>
                <a href="orders.php?status=shipped" class="btn <?php echo ($statusFilter === 'shipped') ? 'active' : ''; ?>">Dikirim</a>
                <a href="orders.php?status=delivered" class="btn <?php echo ($statusFilter === 'delivered') ? 'active' : ''; ?>">Diterima</a>
                <a href="orders.php?status=cancelled" class="btn <?php echo ($statusFilter === 'cancelled') ? 'active' : ''; ?>">Dibatalkan</a>
            </div>
            
            <!-- Orders Table -->
            <div class="product-card" style="padding: 1.5rem;">
                <?php if (!empty($orders)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Kontak</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                <small style="color: #666;"><?php echo htmlspecialchars($order['customer_address']); ?></small>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($order['customer_email']); ?></small><br>
                                <small><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                            </td>
                            <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                            <td>
                                <form method="POST" action="orders.php" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 5px;">
                                        <option value="pending" <?php echo ($order['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo ($order['status'] === 'processing') ? 'selected' : ''; ?>>Diproses</option>
                                        <option value="shipped" <?php echo ($order['status'] === 'shipped') ? 'selected' : ''; ?>>Dikirim</option>
                                        <option value="delivered" <?php echo ($order['status'] === 'delivered') ? 'selected' : ''; ?>>Diterima</option>
                                        <option value="cancelled" <?php echo ($order['status'] === 'cancelled') ? 'selected' : ''; ?>>Dibatalkan</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn" style="padding: 5px 10px;">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; color: #666;">Belum ada pesanan.</p>
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
