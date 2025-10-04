<?php
ob_start();
require_once('includes/load.php');

if($session->isUserLoggedIn(true)) { redirect('home.php', false); }

// Check if code and id exist
if(!isset($_GET['code']) || !isset($_GET['id'])){
    $session->msg("d","Invalid reset link.");
    redirect('index.php');
}

$code = $db->escape($_GET['code']);
$id   = (int)$_GET['id'];

// Find user with matching id and verification code
$user = $db->query("SELECT * FROM users WHERE id='{$id}' AND verification_number='{$code}' LIMIT 1");
$user = $db->fetch_assoc($user);

if(!$user){
    $session->msg("d","Invalid or expired reset link.");
    redirect('index.php');
}

// Process form submission
if(isset($_POST['reset_password'])){
    $password  = trim($_POST['password']);
    $confirm   = trim($_POST['confirm_password']);

    // Validate password
    if(empty($password) || empty($confirm)){
        $session->msg("d","Both fields are required.");
        redirect("reset_password.php?code={$code}&id={$id}");
    }

    if($password !== $confirm){
        $session->msg("d","Passwords do not match.");
        redirect("reset_password.php?code={$code}&id={$id}");
    }

    // Encrypt password using SHA-1
    $h_pass = sha1($password);

    // Update password and clear verification code
    $sql = "UPDATE users SET password='{$h_pass}', verification_number=NULL WHERE id='{$id}'";
    if($db->query($sql)){
        $session->msg("s","Password has been reset successfully. You can now login.");
        redirect('index.php');
    } else {
        $session->msg("d","Failed to reset password. Try again.");
        redirect("reset_password.php?code={$code}&id={$id}");
    }
}

?>

<?php include_once('layouts/header.php'); ?>

<div class="login-page">
    <div class="text-center">
        <h1>Reset Password</h1>
        <h4>Inventory Management System</h4>
        <p>Enter your new password below.</p>
    </div>

    <?php echo display_msg($msg); ?>

    <form method="post" action="" class="clearfix">
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" class="form-control" placeholder="New Password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>

        <div class="form-group mt-3">
            <button type="submit" name="reset_password" class="btn btn-success w-100" style="border-radius:0%">Reset Password</button>
        </div>

        <div class="form-group mt-2 text-center">
            <a href="index.php">Back to Login</a>
        </div>
    </form>
</div>

<?php include_once('layouts/footer.php'); ?>
