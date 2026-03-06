<?php
require_once 'config/database.php';

// Get product ID and quantity from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

// Validate quantity
if ($quantity < 1) {
    $quantity = 1;
}

// Check if product exists and has stock
$stmt = $conn->prepare("SELECT id, name, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Product not found
    header('Location: products.php?error=produk_tidak_ditemukan');
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Check stock
if ($product['stock'] < $quantity) {
    header('Location: product.php?id=' . $productId . '&error=stok_tidak_cukup');
    exit;
}

// Add to cart
addToCart($productId, $quantity);

// Redirect back to cart or product page
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'cart.php';

if ($redirect === 'product') {
    header('Location: product.php?id=' . $productId . '&success=ditambahkan_ke_keranjang');
} else {
    header('Location: cart.php?success=ditambahkan_ke_keranjang');
}
exit;
