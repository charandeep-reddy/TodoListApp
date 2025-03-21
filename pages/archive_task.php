<?php
/**
 * Archive Task
 * This file handles archiving a task
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if task ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php?error=Invalid task ID");
    exit();
}

$task_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if task exists and belongs to the user
$check_query = "SELECT * FROM tasks WHERE task_id = $task_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if(mysqli_num_rows($check_result) != 1) {
    header("Location: dashboard.php?error=Task not found or unauthorized access");
    exit();
}

// Update task to mark as archived
$update_query = "UPDATE tasks SET is_archived = 1 WHERE task_id = $task_id AND user_id = $user_id";

if(mysqli_query($conn, $update_query)) {
    // Archive successful
    header("Location: dashboard.php?success=Task archived successfully!");
} else {
    // Archive failed
    header("Location: dashboard.php?error=Failed to archive task: " . mysqli_error($conn));
}
exit();
?> 