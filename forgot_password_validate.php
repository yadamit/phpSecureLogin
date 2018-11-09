<?php
include_once 'includes/db_connect.php';
include_once 'includes/psl-config.php';

$error_msg = "";
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$email = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Not a valid email
    $error_msg .= '<p class="error">The email address you entered is not valid</p>';
}
echo $email."<br>";

$prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
$stmt = $mysqli->prepare($prep_stmt);


$error_msg = "";
if ($stmt) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows >= 1) {
        // A user with this email address already exists
        $OTP = rand(100000, 999999);
        echo $OTP;
        //mail it somehow
        // phpinfo();
        require '/home/amit/PHPMailer_v5.1/class.phpmailer.php';
        $mail             = new PHPMailer();

        $body             = "<h1>hello, world!</h1>";

        $mail->IsSMTP(); // telling the class to use SMTP

        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "tls";                 // sets the prefix to the servier
        $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port       = 587;                   // set the SMTP port for the GMAIL server
        $mail->Username   = "";  // GMAIL username
        $mail->Password   = "";            // GMAIL password

        $mail->SetFrom('yo.amit.bro@gmail.com', 'First Last');

        // $mail->AddReplyTo("name@yourdomain.com","First Last");

        $mail->Subject    = "PHPMailer Test Subject via smtp (Gmail), basic";

        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

        $mail->MsgHTML($body);

        $address = "yamit@iitk.ac.in";
        $mail->AddAddress($address, "John Doe");

        if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
        echo "Message sent!";
        }
        // $mail = new PHPMailer();
        // $mail->IsSMTP();
        // $mail->SMTPAuth   = true;  
        // $mail->SMTPSecure = "tls";
        // $mail->Host       = "smtp.gmail.com";
        // $mail->Port       = 587;
        // $mail->Username   = "yo.amit.bro";  // GMAIL username
        // $mail->Password   = "Amit@1998"; 
        // $mail->setFrom('yo.amit.bro@gmail.com', 'Amit');
        // // $email->To = "yamit@iitk.ac.in";
        // $mail->AddAddress('yamit@iitk.ac.in', 'My Friend');
        // $mail->Subject  = 'First PHPMailer Message';
        // $body = "First mail";
        // $mail->MsgHTML($body);
        // $mail->AltBody     = 'Hi! This is my first e-mail sent through PHPMailer.';
        // if(!$mail->send()){
        //     echo "<p style='color:red'>Mail sending failed</p><br>.$mail->ErrorInfo";
        //     $error_msg .= '<p class="error">We have sent a mail to your email id with OTP. <br> Enter the OTP here: </p>';
        //     echo '<input type="button" value="validate OTP" onclick="validateOTP('.strval($OTP).')" />';
        // }
        // else{
        //     echo "<p style='color=green'>Mailed successfully</p><br>";
        //     $error_msg .= '<p class="error">We have sent a mail to your email id with OTP. <br> Enter the OTP here: </p>';
        //     echo '<input type="button" value="validate OTP" onclick="validateOTP('.strval($OTP).')" />';
        // }
        // mail($email,  "Password Reset", $OTP, "From: yamit@cse.iitk.ac.in");
    }
    else if($stmt->num_rows < 1){
        $error_msg = '<p class="error"> Email id not registeresd </p>';
    }
} else {
    $error_msg .= '<p class="error">Database error</p>';
}

?>
