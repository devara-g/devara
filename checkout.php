<?php
require_once '/database.php';

$pageTitle = 'Checkout';

// Check if cart is empty
$cartItems = getCartItems($conn);
$cartTotal = getCartTotal($conn);

if (empty($cartItems)) {
    header('Location: products.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name']);
    $customerEmail = trim($_POST['customer_email']);
    $customerPhone = trim($_POST['customer_phone']);
    $customerAddress = trim($_POST['customer_address']);

    // Validate inputs
    $errors = [];
    if (empty$customerName)) {
        $errors[] = 'Nama lengkap wajib diisi';
    }
    if (empty($customerEmail)) {
        $errors[] = 'Email wajib diisi';
    } elseif (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    if (empty($customerPhone)) {
        $errors[] = 'Nomor telepon wajib diisi';
    }
    if (empty($customerAddress)) {
        $errors[] = 'Alamat wajib diisi';
    }

    // If no errors, process the order
    if (empty($errors)) {
        $totalAmount = $cartTotal * 1.1; // Including 10% tax

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ssssd", $customerName, $customerEmail, $customerPhone, $customerAddress, $totalAmount);

        if ($stmt->execute()) {
            $orderId = $stmt->insert_id;
            $stmt->close();

            // Insert order items
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");

            foreach ($cartItems as $item) {
                $stmt->bind_param("iissid", $orderId, $item['id'], $item['name'], $item['price'], $item['quantity'], $item['subtotal']);
                $stmt->execute();

                // Update product stock
                $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $updateStock->bind_param("ii", $item['quantity'], $item['id']);
                $updateStock->execute();
                $updateStock->close();
            }
            $stmt->close();

            // Clear cart
            $_SESSION['cart'] = [];

            // Redirect to success page
            header('Location: order_success.php?order_id=' . $orderId);
            exit;
        } else {
            $errors[] = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>

<?php include 'components/header.php'; ?>

<section>
    <div class="container">
        <div class="section-title">
            <h2>Checkout</h2>
            <p>Isi data diri untuk menyelesaikan pesanan</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin: 0; padding-left: 1rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Checkout Form -->
            <div class="checkout-container">
                <h3 style="margin-bottom: 1.5rem; color: var(--accent-color);">Data Pengiriman</h3>

                <form method="POST" action="checkout.php">
                    <div class="form-group">
                        <label for="customer_name">Nama Lengkap *</label>
                        <input type="text" id="customer_name" name="customer_name" value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer_email">Email *</label>
                            <input type="email" id="customer_email" name="customer_email" value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="customer_phone">Nomor Telepon *</label>
                            <input type="tel" id="customer_phone" name="customer_phone" value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_address">Alamat Lengkap *</label>
                        <textarea id="customer_address" name="customer_address" rows="4" required><?php echo isset($_POST['customer_address']) ? htmlspecialchars($_POST['customer_address']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-check"></i> Pesan Sekarang
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="cart-summary" style="height: fit-content;">
                <h3>Ringkasan Pesanan</h3>

                <?php foreach ($cartItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                        <div>
                            <p style="font-weight: 600;"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p style="color: #666; font-size: 0.9rem;"><?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                        </div>
                        <div style="font-weight: 600;">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></div>
                    </div>
                <?php endforeach; ?>

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
            </div>
        </div>
    </div>
</section>


<?php include 'components/footer.php'; ?>
