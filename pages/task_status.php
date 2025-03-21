<?php
/**
 * Task Status Update
 * This file handles updating task status
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if task ID and status are provided
if(!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['status']) || empty($_GET['status'])) {
    header("Location: dashboard.php?error=Invalid task ID or status");
    exit();
}

$task_id = (int)$_GET['id'];
$status = mysqli_real_escape_string($conn, $_GET['status']);
$user_id = $_SESSION['user_id'];

// Validate status
$valid_statuses = array('Pending', 'On-going', 'Completed');
if(!in_array($status, $valid_statuses)) {
    header("Location: dashboard.php?error=Invalid status value");
    exit();
}

// Check if task exists and belongs to the user
$check_query = "SELECT * FROM tasks WHERE task_id = $task_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if(mysqli_num_rows($check_result) != 1) {
    header("Location: dashboard.php?error=Task not found or unauthorized access");
    exit();
}

// Update task status
$update_query = "UPDATE tasks SET status = '$status' WHERE task_id = $task_id AND user_id = $user_id";

if(mysqli_query($conn, $update_query)) {
    // Status update successful
    header("Location: dashboard.php?success=Task status updated to $status!");
} else {
    // Status update failed
    header("Location: dashboard.php?error=Failed to update task status: " . mysqli_error($conn));
}
exit();
?> 