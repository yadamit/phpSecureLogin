<?php
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
 
$mail = new PHPMailer(true); 
try {
    //Server settings
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.cse.iitk.ac.in';
    $mail->SMTPAuth = true;
    $mail->Username = 'yamit@cse.iitk.ac.in';
    $mail->Password = 'Amit1998';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 25;
 
 
    $mail->setFrom('yamit@cse.iitk.ac.in', 'Admin');
    $mail->addAddress('yamit@iitk.ac.in', 'Recipient1');
    // $mail->addAddress('recipient2@example.com');
    // $mail->addReplyTo('noreply@example.com', 'noreply');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');
 
    //Attachments
    // $mail->addAttachment('/backup/myfile.tar.gz');
 
    //Content
    $mail->isHTML(true); 
    $mail->Subject = 'Test Mail Subject!';
    $mail->Body    = 'This is SMTP Email Test';
 
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}