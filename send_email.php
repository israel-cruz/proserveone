<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405); // Método no permitido
    exit("Method Not Allowed");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

    $recaptcha = $_POST['g-recaptcha-response'];

    $secret_key = '6LdS7uIqAAAAAJXgZRcuRmlURVkP2T2DImP6iFHX';

    $url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$recaptcha;

    $response = file_get_contents($url);

    $response = json_decode($response);

    if ($response->success) {
        $mail = new PHPMailer(true);

        try {
            // SMTP setup
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'servicehub@proserveone.com'; 
            $mail->Password = 'lsdk qngr rvat wogw'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Form data
            $firstName = htmlspecialchars($_POST['formFirstName']);
            $lastName = htmlspecialchars($_POST['formLastName']);
            $email = htmlspecialchars($_POST['formEmail']);
            $phone = htmlspecialchars($_POST['formPhone']);
            $message = nl2br(htmlspecialchars($_POST['formMessages']));


            $mail->setFrom($email, "$firstName $lastName");
            $mail->addAddress('servicehub@proserveone.com');

            // email
            $mail->isHTML(true);
            $mail->Subject = "New message from $firstName $lastName";
            $mail->Body = "<h1>ProServe One - New message!</h1>
                        <p><strong>Name:</strong> $firstName $lastName</p>
                        <p><strong>Email:</strong> $email</p>
                        <p><strong>Phone:</strong> $phone</p>
                        <p><strong>Message:</strong><br>$message</p>";

            // $mail->SMTPDebug = 2; 
            // $mail->Debugoutput = 'html';

            

            // send email
            if ($mail->send()) {
                echo "Message sent successfully.";
            } else {
                echo "Error: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo "Error: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['status'] = "Error in recaptcha verification";
        header("Location: {$_SERVER["HTTP_REFERER"]}");
        exit(0);
    }
}

?>
