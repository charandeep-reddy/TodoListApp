<?php
/**
 * Add Comment Process
 * This file handles adding comments to tasks
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if form is submitted
if(isset($_POST['add_comment']) && isset($_POST['task_id']) && isset($_POST['comment'])) {
    $task_id = (int)$_POST['task_id'];
    $user_id = $_SESSION['user_id'];
    $comment_text = mysqli_real_escape_string($conn, trim($_POST['comment']));
    
    // Validate comment
    if(empty($comment_text)) {
        header("Location: task_details.php?id=$task_id&error=Comment cannot be empty");
        exit();
    }
    
    // Check if task exists and belongs to the user
    $check_query = "SELECT * FROM tasks WHERE task_id = $task_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) != 1) {
        header("Location: dashboard.php?error=Task not found or unauthorized access");
        exit();
    }
    
    // Insert comment
    $insert_query = "INSERT INTO task_comments (task_id, user_id, comment_text) VALUES ($task_id, $user_id, '$comment_text')";
    
    if(mysqli_query($conn, $insert_query)) {
        // Comment added successfully
        header("Location: task_details.php?id=$task_id&success=Comment added successfully");
    } else {
        // Failed to add comment
        header("Location: task_details.php?id=$task_id&error=Failed to add comment: " . mysqli_error($conn));
    }
    exit();
} else {
    // Invalid request
    header("Location: dashboard.php");
    exit();
}
?> 