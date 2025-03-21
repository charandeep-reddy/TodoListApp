<?php
/**
 * Delete Task
 * This file handles permanently deleting a task
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
    header("Location: archived_tasks.php?error=Invalid task ID");
    exit();
}

$task_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if task exists and belongs to the user
$check_query = "SELECT * FROM tasks WHERE task_id = $task_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if(mysqli_num_rows($check_result) != 1) {
    header("Location: archived_tasks.php?error=Task not found or unauthorized access");
    exit();
}

// Delete the task (this will also delete associated reminders due to foreign key constraints)
$delete_query = "DELETE FROM tasks WHERE task_id = $task_id AND user_id = $user_id";

if(mysqli_query($conn, $delete_query)) {
    // Deletion successful
    header("Location: archived_tasks.php?success=Task permanently deleted!");
} else {
    // Deletion failed
    header("Location: archived_tasks.php?error=Failed to delete task: " . mysqli_error($conn));
}
exit();
?> 