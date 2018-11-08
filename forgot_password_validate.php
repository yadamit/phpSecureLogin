<?php
include_once 'includes/db_connect.php';
include_once 'includes/psl-config.php';

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
        $error_msg .= '<p class="error">We have sent a mail to your email id with OTP. <br> Enter the OTP here: </p>';
        $OTP = rand(100000, 999999);
        //mail it somehow
        mail($email,  "Password Reset", $OTP, "From: yamit@cse.iitk.ac.in");
    }
    else if($stmt->num_rows < 1){
        $error_msg = '<p class="error"> Email id not registeresd </p>';
    }
} else {
    $error_msg .= '<p class="error">Database error</p>';
}

?>