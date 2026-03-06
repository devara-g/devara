<?php
require_once 'config/database.php';

$pageTitle = 'Pesanan Berhasil';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Get order details
$order = null;
if ($orderId > 0) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<?php include 'components/header.php'; ?>

<section>
    <div class="container">
        <div class="success-container">
            <i class="fas fa-check-circle"></i>
            <h2>Pesanan Berhasil!</h2>
            <p>Terima kasih telah melakukan pemesanan. Pesanan Anda telah kami terima dan akan diproses segera.</p>

            <?php if ($order): ?>
                <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 500px; margin: 2rem auto; box-shadow: var(--shadow);">
                    <h3 style="margin-bottom: 1rem; color: var(--accent-color);">Detail Pesanan</h3>
                    <p><strong>Nomor Pesanan:</strong> #<?php echo $orderId; ?></p>
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
                    <p><strong>Status:</strong> <span style="color: var(--success-color);"><?php echo ucfirst($order['status']); ?></span></p>
                </div>
            <?php endif; ?>

            <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
            <a href="products.php" class="btn">Lanjut Belanja</a>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>