<?php
require_once('includes/load.php');

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure PHPMailer is installed via Composer

if(isset($_POST['email'])){
    $email = $db->escape($_POST['email']);
    $email = trim($email);

    // Check if email exists
    $user = find_by_email('users', $email); // we'll create this helper
    if(!$user){
        $session->msg("d","Email not found.");
        redirect('forgot_password.php');
    }

    // Generate verification code
    $verification_code = rand(100000,999999);

    // Update user with verification_number
    $sql = "UPDATE users SET verification_number='{$verification_code}' WHERE id='{$user['id']}'";
    $db->query($sql);

    // Prepare email
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'johnzausapogi@gmail.com';
        $mail->Password   = 'chsm uhit eyyn yhmj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('no-reply@example.com', 'Inventory System');
        $mail->addAddress($user['email'], $user['name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Hello {$user['name']},<br><br>
                          Click the link below to reset your password:<br>
                          <a href='http://localhost/InventorySystem_PHP/reset_password.php?code={$verification_code}&id={$user['id']}'>Reset Password</a><br><br>
                          If you didn't request this, ignore this email.";

        $mail->send();
        $session->msg("s","Reset link has been sent to your email.");
        redirect('forgot_password.php');

    } catch (Exception $e) {
        $session->msg("d","Mailer Error: {$mail->ErrorInfo}");
        redirect('forgot_password.php');
    }

} else {
    redirect('forgot_password.php');
}
