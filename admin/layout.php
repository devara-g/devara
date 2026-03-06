<?php
// Admin layout with sidebar
require_once '../config/database.php';

$pageTitle = $pageTitle ?? 'Admin';
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
                    <li><a href="<?php echo BASE_URL; ?>/admin/orders.php">Pesanan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php" target="_blank">Lihat Website</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
            <?php echo $content; ?>
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
