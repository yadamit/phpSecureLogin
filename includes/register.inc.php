<?php

/* 
 * Copyright (C) 2013 peter
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once 'db_connect.php';
include_once 'psl-config.php';

$error_msg = "";
$suggested_usernames = "";
$pass_arr = array("123456","123456789","qwerty","12345678","111111","1234567890","1234567","password","123123","987654321","qwertyuio","mynoob","123321","666666","18atcskd2w","7777777","1q2w3e4r","654321","555555","3rjs1la7qe","google","1q2w3e4r5t","123qwe","zxcvbnm","1q2w3e");



if (isset($_POST['username'], $_POST['email'], $_POST['p'])) {
    // Sanitize and validate the data passed in
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Not a valid email
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
    $pwd = $_POST['p'];
    
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
    $shortest =-1;
    foreach($pass_arr as $pass){
        $lev = levenshtein($password,$pass);
        if($lev == 0){
            $shortest =0;
            break;
        }
        if($lev <= $shortest || $shortest < 0){
            $shortest = $lev;
        }


    }
    $pass_status = "";
    if($shortest <= 3){
        $pass_status = "WEAK";
    }
    elseif($shortest <= 6){
        $pass_status = "MODERATE";
    }
    elseif($shortest <= 15  ){
        $pass_status = "STRONG";
    }

    // Username validity and password validity have been checked client side.
    // This should should be adequate as nobody gains any advantage from
    // breaking these rules.
    //
    
    $prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);

    $prep_stmt2 = "SELECT id FROM members WHERE username = ? LIMIT 1";
    $stmt2 = $mysqli->prepare($prep_stmt2);
    
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            // A user with this email address already exists
            $error_msg .= '<p class="error">A user with this email address already exists.</p>';
        }
    } else {
        $error_msg .= '<p class="error">Database error</p>';
    }

    if ($stmt2) {
        $stmt2->bind_param('s', $username);
        $stmt2->execute();
        $stmt2->store_result();
        
        if ($stmt2->num_rows == 1) {
            // A user with this username address already exists
            $error_msg .= '<p class="error">A user with this username already exists.</p>';
            $i = 0;
            $suggested_usernames .= '<p class="error">Suggested usernames: ';
            $available_prefix = array("123_", "boss_", "yoyo_", "stud_","123","great_","12_","00_","12","sim_");
            $available_suffix = array("");
            while($i < 4){
                $suggest = rand(0, 9);
                $suggested_name = $available_prefix[$suggest] . $username;
                $prep_stmt = "SELECT id FROM members WHERE username = ? LIMIT 1";
                $stmt = $mysqli->prepare($prep_stmt);
                if ($stmt) {
                    $stmt->bind_param('s', $suggested_name);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    if (!$stmt->num_rows == 1) {
                        $i++;
                        $suggested_usernames = $suggested_usernames . '  ,  ' . $suggested_name;
                    }
                } else {
                    $error_msg .= '<p class="error">Database error</p>';
                }
            }
            $suggested_usernames = $suggested_usernames . '</p>';
            

        }
    } else {
        $error_msg .= '<p class="error">Database error</p>';
    }


    
    // TODO: 
    // We'll also have to account for the situation where the user doesn't have
    // rights to do registration, by checking what type of user is attempting to
    // perform the operation.

    if (empty($error_msg)) {
        // Create a random salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

        // Create salted password 
        $password = hash('sha512', $password . $random_salt);

        // Insert the new user into the database 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                header('Location: ../error.php?err=Registration failure: INSERT');
                exit();
            }
        }
        header('Location: ./register_success.php');
        exit();
    }
}