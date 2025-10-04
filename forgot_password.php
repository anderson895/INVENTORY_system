<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('home.php', false);}
?>
<?php include_once('layouts/header.php'); ?>

<div class="login-page">
    <div class="text-center">
       <h1>Forgot Password</h1>
       <h4>Inventory Management System</h4>
       <p>Enter your registered email to reset your password</p>
    </div>

    <?php echo display_msg($msg); ?>

    <form method="post" action="forgot_password_process.php" class="clearfix">
        <div class="form-group">
            <label for="email" class="control-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary w-100" style="border-radius:0%">Send Reset Link</button>
        </div>

        <div class="form-group mt-2 text-center">
            <a href="index.php">Back to Login</a>
        </div>
    </form>
</div>

<?php include_once('layouts/footer.php'); ?>
