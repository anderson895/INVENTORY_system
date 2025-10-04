<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php
require_once('includes/load.php');

$product = find_by_id('products', (int)$_GET['product_id']);
$all_categories = find_all('categories');
$all_photo = find_all('media');

if (!$product) {
    $session->msg("d", "Missing product id.");
    redirect('product.php');
}
?>

<div class="container my-4">
    <?php echo display_msg($msg); ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
           
            <h4 class="mb-0">Product Details</h4>
        </div>
        <div class="card-body">
            <div class="row g-4">
               
                <!-- Product Info -->
                <div class="col-md-8">
                    <h3 class="mb-3"><?php echo remove_junk($product['name']); ?></h3>
                    <p><strong>Category:</strong> 
                        <?php 
                        $category = find_by_id('categories', $product['categorie_id']);
                        echo $category ? remove_junk($category['name']) : "N/A"; 
                        ?>
                    </p>
                    <p><strong>Quantity:</strong> <?php echo remove_junk($product['quantity']); ?></p>
                    <p><strong>Buying Price:</strong> $<?php echo remove_junk($product['buy_price']); ?>.00</p>
                    <p><strong>Selling Price:</strong> $<?php echo remove_junk($product['sale_price']); ?>.00</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
