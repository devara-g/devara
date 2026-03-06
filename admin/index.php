<?php
require_once '../config/database.php';

$pageTitle = 'Admin - Dashboard';

// Get statistics
$totalProducts = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($result && $row = $result->fetch_assoc()) {
    $totalProducts = $row['count'];
}

$totalOrders = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
if ($result && $row = $result->fetch_assoc()) {
    $totalOrders = $row['count'];
}

$totalRevenue = 0;
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
if ($result && $row = $result->fetch_assoc() && $row['total']) {
    $totalRevenue = $row['total'];
}

$pendingOrders = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
if ($result && $row = $result->fetch_assoc()) {
    $pendingOrders = $row['count'];
}

// Get recent orders
$recentOrders = [];
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}
?>

<?php include '../components/header.php'; ?>

<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <h2 style="margin-bottom: 2rem; color: var(--accent-color);">Admin Dashboard</h2>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="product-card" style="padding: 1.5rem; text-align: center;">
            <i class="fas fa-coffee" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $totalProducts; ?></h3>
            <p style="color: #666;">Total Produk</p>
        </div>

        <div class="product-card" style="padding: 1.5rem; text-align: center;">
            <i class="fas fa-shopping-cart" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $totalOrders; ?></h3>
            <p style="color: #666;">Total Pesanan</p>
        </div>

        <div class="product-card" style="padding: 1.5rem; text-align: center;">
            <i class="fas fa-clock" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $pendingOrders; ?></h3>
            <p style="color: #666;">Pesanan Pending</p>
        </div>

        <div class="product-card" style="padding: 1.5rem; text-align: center;">
            <i class="fas fa-money-bill" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></h3>
            <p style="color: #666;">Total Pendapatan</p>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="product-card" style="padding: 1.5rem;">
        <h3 style="margin-bottom: 1.5rem; color: var(--accent-color);">Pesanan Terbaru</h3>

        <?php if (!empty($recentOrders)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                            <td>
                                <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; 
                            background: <?php echo $order['status'] === 'pending' ? '#ffc107' : ($order['status'] === 'delivered' ? '#28a745' : '#17a2b8'); ?>;
                            color: <?php echo $order['status'] === 'pending' ? '#000' : '#fff'; ?>;">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn" style="padding: 5px 10px;">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                <a href="orders.php" class="btn btn-primary">Lihat Semua Pesanan</a>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666;">Belum ada pesanan.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../components/footer.php'; ?>