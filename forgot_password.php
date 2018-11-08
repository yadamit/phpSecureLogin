<?php
    include_once 'includes/db_connect.php';
    include_once 'includes/functions.php';
    include_once 'forgot_password_validate.php';
    echo "So you forgot your password";
?>

<!DOCTYPE html>
<html>
    <body>
        <form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="forgot_password">
        Enter your E-mail-ID : <input type="text" name="email" />
        <input type="submit" 
                   value="Submit"/>
        </form>
        <?php
            if(!empty($error_msg)){
                echo $error_msg;
            }
        ?>
    </body>
</html>