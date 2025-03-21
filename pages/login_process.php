<?php
/**
 * Login Process
 * This file processes user login
 */

// Include database configuration
require_once('../includes/config.php');

// Check if the form is submitted
if(isset($_POST['login'])) {
    // Get form data and sanitize inputs
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    
    // Validate inputs
    $errors = array();
    
    // Validate username
    if(empty($username)) {
        $errors[] = "Username is required";
    }
    
    // Validate password
    if(empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no errors, proceed with login
    if(empty($errors)) {
        // Check if the user exists
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            // Verify password
            if(password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Invalid password
                $errors[] = "Invalid username or password";
            }
        } else {
            // User not found
            $errors[] = "Invalid username or password";
        }
    }
    
    // If there are errors, redirect back to login page with error message
    if(!empty($errors)) {
        $error_message = implode("<br>", $errors);
        header("Location: ../index.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    // If the form is not submitted, redirect to login page
    header("Location: ../index.php");
    exit();
}
?> 