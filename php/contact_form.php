<?php
require 'vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $handle = "UTUBE2MP3SITE HelpDesk";
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    $errors = [];

    // Validate name
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        $errors['name'] = 'Name must only contain letters and spaces.';
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    // Validate message
    if (empty($message)) {
        $errors['message'] = 'Message is required.';
    } elseif (strlen($message) < 10 || strlen($message) > 500) {
        $errors['message'] = 'Message must be between 10 and 500 characters.';
    }

    if (empty($errors)) {
        // Email configuration
        $to = $_ENV['EMAIL_USER'];
        $subject = "UTUBE2MP3SITE HelpDesk for $name";

        // PHPMailer instance
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        // SMTP settings
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port = $_ENV['SMTP_PORT'];

        // Email setup
        $mail->setFrom($email, $handle);
        $mail->addAddress($to); // Add recipient
        $mail->addReplyTo($email);

        // Embed the logo
        $mail->addEmbeddedImage(__DIR__ . '/local/logo.png', 'logo_cid', 'logo.png');

        // Email content 
        $mail->isHTML(true); // Enable HTML content
        $mail->Subject = $subject;
        $mail->Body = "
            <div style=\"font-family: Arial, sans-serif; color: #333; line-height: 1.6;\">
                
                <h2 style=\"color:rgb(172, 2, 2);\">New Message from $name</h2>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong> $message</p>
                
                <hr style=\"border: none; border-top: 1px solid #ddd; margin: 20px 0;\" />
                <p style=\"text-align: center; color: #777; font-size: 12px;\">This message was sent via UTUBE2MP3SITE HelpDesk.</p>
                <div style=\"text-align: center; margin: 5px;\">
                    <img src=\"cid:logo_cid\" alt=\"UTUBE2MP3SITE Logo\" style=\"width: 200px; height: auto;\" />
                </div>
            </div>
        ";

        // Send the email
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    } else {
        // Show form errors
        foreach ($errors as $field => $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
    }
} else {
    echo 'Invalid Request';
}
?>