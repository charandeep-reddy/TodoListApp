<?php
/**
 * Registration Process
 * This file processes user registration
 */

// Include database configuration
require_once('../includes/config.php');

// Check if the form is submitted
if(isset($_POST['register'])) {
    // Get form data and sanitize inputs
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate inputs
    $errors = array();
    
    // Validate username
    if(empty($username)) {
        $errors[] = "Username is required";
    } elseif(strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters";
    }
    
    // Validate email
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if username or email already exists
    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        $user = mysqli_fetch_assoc($check_result);
        if($user['username'] == $username) {
            $errors[] = "Username already exists";
        }
        if($user['email'] == $email) {
            $errors[] = "Email already exists";
        }
    }
    
    // Validate password
    if(empty($password)) {
        $errors[] = "Password is required";
    } elseif(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    // Validate password confirmation
    if($password != $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // If no errors, proceed with registration
    if(empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        
        if(mysqli_query($conn, $insert_query)) {
            // Registration successful, redirect to login page
            header("Location: ../index.php?success=Registration successful! Please login.");
            exit();
        } else {
            // Registration failed
            $errors[] = "Registration failed: " . mysqli_error($conn);
        }
    }
    
    // If there are errors, redirect back to registration page with error message
    if(!empty($errors)) {
        $error_message = implode("<br>", $errors);
        header("Location: register.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    // If the form is not submitted, redirect to registration page
    header("Location: register.php");
    exit();
}
?> 