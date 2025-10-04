<?php
require_once('includes/load.php');

require_once 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;


// page_require_level(1);
header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['requestType'])) {
        echo json_encode(['status' => 400, 'message' => 'Missing request type.']);
        exit;
    }

    /* ==================== ADD GROUP ==================== */
        if ($_POST['requestType'] == 'add_product') {
                

            $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'selling-price');
            validate_fields($req_fields);

            $p_name   = remove_junk($db->escape($_POST['product-title']));
            $p_cat    = remove_junk($db->escape($_POST['product-categorie']));
            $p_qty    = remove_junk($db->escape($_POST['product-quantity']));
            $p_buy    = remove_junk($db->escape($_POST['buying-price']));
            $p_sale   = remove_junk($db->escape($_POST['selling-price']));
            $media_id = empty($_POST['product-photo']) ? 0 : remove_junk($db->escape($_POST['product-photo']));
            $date     = make_date();

            // Insert product
            $query  = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, media_id, date) ";
            $query .= "VALUES ('{$p_name}', '{$p_qty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$media_id}', '{$date}')";

            if ($db->query($query)) {
               $product_id = $db->insert_id();

                // --- Generate QR code (Endroid v6) using named arguments ---
                $qrDir = __DIR__ . '/qr_codes/';
                if (!is_dir($qrDir)) mkdir($qrDir, 0755, true);

                // The URL to encode in the QR code
                $qrData = "http://localhost/InventorySystem_PHP/view_product.php?product_id={$product_id}";

                $qrCode = new QrCode(
                    data: $qrData, // <-- use the URL here
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::High,
                    size: 300,
                    margin: 10,
                    roundBlockSizeMode: RoundBlockSizeMode::Margin,
                    foregroundColor: new Color(0, 0, 0),
                    backgroundColor: new Color(255, 255, 255)
                );

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $qrPath = $qrDir . $product_id . '.png';
                $result->saveToFile($qrPath);

                echo json_encode([
                    'status'  => 200,
                    'message' => 'Product added successfully!',
                    'qr_code' => 'qr_codes/' . $product_id . '.png'
                ]);
            } else {
                echo json_encode([
                    'status'  => 400,
                    'message' => 'Failed to add product. Please try again.'
                ]);
            }
        }else if ($_POST['requestType'] == 'update_product') {
                $req_fields = array('product-id', 'product-title', 'product-categorie', 'product-quantity', 'buying-price', 'selling-price');
                validate_fields($req_fields);

                if (empty($errors)) {
                    $p_id   = (int)$_POST['product-id'];
                    $p_name = remove_junk($db->escape($_POST['product-title']));
                    $p_cat  = (int)$_POST['product-categorie'];
                    $p_qty  = remove_junk($db->escape($_POST['product-quantity']));
                    $p_buy  = remove_junk($db->escape($_POST['buying-price']));
                    $p_sale = remove_junk($db->escape($_POST['selling-price']));

                    // Optional product photo
                    $media_id = empty($_POST['product-photo']) ? 0 : remove_junk($db->escape($_POST['product-photo']));

                    // Update product in DB
                    $query  = "UPDATE products SET ";
                    $query .= "name='{$p_name}', ";
                    $query .= "quantity='{$p_qty}', ";
                    $query .= "buy_price='{$p_buy}', ";
                    $query .= "sale_price='{$p_sale}', ";
                    $query .= "categorie_id='{$p_cat}', ";
                    $query .= "media_id='{$media_id}' ";
                    $query .= "WHERE id='{$p_id}'";

                    $result = $db->query($query);

                    if ($result && $db->affected_rows() >= 0) {
                        // --- Generate or replace QR code ---
                        $qrDir = __DIR__ . '/qr_codes/';
                        if (!is_dir($qrDir)) mkdir($qrDir, 0755, true);

                        $qrPath = $qrDir . $p_id . '.png';

                        // URL to encode in the QR code
                        $qrData = "http://localhost/InventorySystem_PHP/view_product.php?product_id={$p_id}";

                        $qrCode = new QrCode(
                            data: $qrData, // <-- use the URL here
                            encoding: new Encoding('UTF-8'),
                            errorCorrectionLevel: ErrorCorrectionLevel::High,
                            size: 300,
                            margin: 10,
                            roundBlockSizeMode: RoundBlockSizeMode::Margin,
                            foregroundColor: new Color(0, 0, 0),
                            backgroundColor: new Color(255, 255, 255)
                        );

                        $writer = new PngWriter();
                        $resultQR = $writer->write($qrCode);

                        // Save QR code (overwrite if exists)
                        $resultQR->saveToFile($qrPath);


                        echo json_encode([
                            'status' => 200,
                            'message' => 'Product updated successfully!',
                            'qr_code' => 'qr_codes/' . $p_id . '.png'
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 400,
                            'message' => 'No changes made or update failed.'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 400,
                        'message' => implode(', ', $errors)
                    ]);
                }

                exit;
    }
    /* ==================== ADD GROUP ==================== */
    
    else if ($_POST['requestType'] == 'Add_group') {
        $req_fields = array('group-name','group-level');
        validate_fields($req_fields);

        if (!empty($errors)) {
            echo json_encode(['status' => 400, 'message' => implode(', ', $errors)]);
            exit;
        }

         $name   = remove_junk($db->escape($_POST['group-name']));
        $level  = remove_junk($db->escape($_POST['group-level']));
        $status = isset($_POST['status']) ? remove_junk($db->escape($_POST['status'])) : 1;

        // ✅ FIXED: Use same working logic as original
        // The old code checks for "false" (not found). So we keep that logic.

        if (find_by_groupName($name) === false) {
            echo json_encode([
                'status' => 400,
                'message' => 'Entered Group Name already exists!'
            ]);
            exit;
        }

        if (find_by_groupLevel($level) === false) {
            echo json_encode([
                'status' => 400,
                'message' => 'Entered Group Level already exists!'
            ]);
            exit;
        }

        $query  = "INSERT INTO user_groups (group_name, group_level, group_status)
                   VALUES ('{$name}', '{$level}', '{$status}')";
        if ($db->query($query)) {
            echo json_encode(['status' => 200, 'message' => 'Group has been created successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to create group.']);
        }
        exit;
    }

    /* ==================== UPDATE GROUP ==================== */
    elseif ($_POST['requestType'] == 'Update_group') {
        $req_fields = array('group-id','group-name','group-level');
        validate_fields($req_fields);

        if (!empty($errors)) {
            echo json_encode(['status' => 400, 'message' => implode(', ', $errors)]);
            exit;
        }

        $id = (int)$db->escape($_POST['group-id']);
        $name  = remove_junk($db->escape($_POST['group-name']));
        $level = remove_junk($db->escape($_POST['group-level']));
        $status = isset($_POST['status']) ? remove_junk($db->escape($_POST['status'])) : 1;

        $existing = find_by_id('user_groups', $id);
        if (!$existing) {
            echo json_encode(['status' => 404, 'message' => 'Group not found.']);
            exit;
        }

        // Optional: check duplicates (ignore same record)
        $dupName = $db->query("SELECT id FROM user_groups WHERE group_name='{$name}' AND id!='{$id}' LIMIT 1");
        if ($db->num_rows($dupName) > 0) {
            echo json_encode(['status' => 400, 'message' => 'Group Name already exists!']);
            exit;
        }

        $dupLevel = $db->query("SELECT id FROM user_groups WHERE group_level='{$level}' AND id!='{$id}' LIMIT 1");
        if ($db->num_rows($dupLevel) > 0) {
            echo json_encode(['status' => 400, 'message' => 'Group Level already exists!']);
            exit;
        }

        $query = "UPDATE user_groups SET 
                    group_name='{$name}', 
                    group_level='{$level}', 
                    group_status='{$status}'
                  WHERE id='{$id}'";
        if ($db->query($query)) {
            echo json_encode(['status' => 200, 'message' => 'Group updated successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to update group.']);
        }
        exit;
    }else if ($_POST['requestType'] == 'add_user') {
        $name = remove_junk($db->escape($_POST['full-name']));
        $username = remove_junk($db->escape($_POST['username']));
        $email = remove_junk($db->escape($_POST['email']));
        $password = sha1($_POST['password']);
        $level = (int)$db->escape($_POST['level']);

        

        $sql = "INSERT INTO users (name, username,email, password, user_level, status)
                VALUES ('{$name}','{$username}','{$email}','{$password}','{$level}','1')";
        if ($db->query($sql)) {
            echo json_encode(['status' => 200, 'message' => 'User added successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to add user.']);
        }
        exit;
    }else if ($_POST['requestType'] == 'update_user') {
        $id = (int)$_POST['user_id'];
        $name = remove_junk($db->escape($_POST['name']));
        $username = remove_junk($db->escape($_POST['username']));
        $email = remove_junk($db->escape($_POST['email']));
        $level = (int)$db->escape($_POST['level']);
        $status = (int)$db->escape($_POST['status']);

        $sql = "UPDATE users SET 
                name='{$name}', username='{$username}', email='{$email}', 
                user_level='{$level}', status='{$status}' 
                WHERE id='{$id}'";

        if ($db->query($sql)) {
            echo json_encode(['status' => 200, 'message' => 'User updated successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to update user.']);
        }
        exit;
    }else if ($_POST['requestType'] == 'update_categorie') {
            $req_fields = array('categorie-name', 'id');
            validate_fields($req_fields);

            $cat_name = remove_junk($db->escape($_POST['categorie-name']));
            $cat_id   = (int)$_POST['id'];

            if (empty($errors)) {
                $sql = "UPDATE categories SET name = '{$cat_name}' WHERE id = '{$cat_id}' LIMIT 1";
                $result = $db->query($sql);

                if ($result && $db->affected_rows() === 1) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Successfully updated category.'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 400,
                        'message' => 'No changes made or update failed.'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 400,
                    'message' => $errors
                ]);
            }
            exit;
        }else if ($_POST['requestType'] === 'add_sale') {
                $req_fields = array('s_id', 'quantity', 'price', 'total', 'date');
                validate_fields($req_fields);

                if (empty($errors)) {
                    // Sanitize inputs
                    $p_id    = (int) $db->escape($_POST['s_id']);
                    $s_qty   = (int) $db->escape($_POST['quantity']);
                    $s_price = (float) $db->escape($_POST['price']);
                    $s_total = (float) $db->escape($_POST['total']);
                    $date    = $db->escape($_POST['date']);
                    $s_date  = make_date(); // Current timestamp

                    // Insert into sales table
                    $sql  = "INSERT INTO sales (product_id, qty, price, date) VALUES (
                                '{$p_id}', '{$s_qty}', '{$s_total}', '{$s_date}'
                            )";

                    if ($db->query($sql)) {
                        // Update product quantity
                        update_product_qty($s_qty, $p_id);

                        echo json_encode([
                            'status' => 200,
                            'message' => "Sale added successfully."
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 500,
                            'message' => "Sorry, failed to add sale."
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 422,
                        'message' => implode(", ", $errors)
                    ]);
                }
                exit; // Stop further execution
            }






    /* ==================== INVALID TYPE ==================== */
    else {
        echo json_encode(['status' => 400, 'message' => 'Invalid request type.']);
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

   if (isset($_GET['requestType']))
    {
        if ($_GET['requestType'] == 'get_user' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $user = find_by_id('users', $id);
            if ($user) {
                echo json_encode(['status' => 200, 'user' => $user]);
            } else {
                echo json_encode(['status' => 404, 'message' => 'User not found.']);
            }
            exit;
        }else if ($_GET['requestType'] == 'get_product' && isset($_GET['id'])) {
            $product = find_by_id('products', (int)$_GET['id']);
            $all_categories = find_all('categories');
            $all_photo = find_all('media');

            if ($product) {
                echo json_encode([
                    'status' => 200,
                    'product' => $product,
                    'all_categories' => $all_categories,
                    'all_photo' => $all_photo
                ]);
            } else {
                echo json_encode([
                    'status' => 404,
                    'message' => 'Product not found.'
                ]);
            }
            exit;
        }else{
            echo "404";
        }
    }else {
        echo 'No GET REQUEST';
    }
}
?>
