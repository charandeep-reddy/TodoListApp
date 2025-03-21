<?php
/**
 * Delete Reminder
 * This file handles deleting a reminder
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if reminder ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: reminders.php?error=Invalid reminder ID");
    exit();
}

$reminder_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if reminder exists and belongs to the user's task
$check_query = "SELECT r.* FROM reminders r
                JOIN tasks t ON r.task_id = t.task_id
                WHERE r.reminder_id = $reminder_id AND t.user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if(mysqli_num_rows($check_result) != 1) {
    header("Location: reminders.php?error=Reminder not found or unauthorized access");
    exit();
}

// Delete the reminder
$delete_query = "DELETE FROM reminders WHERE reminder_id = $reminder_id";

if(mysqli_query($conn, $delete_query)) {
    // Deletion successful
    header("Location: reminders.php?success=Reminder deleted successfully!");
} else {
    // Deletion failed
    header("Location: reminders.php?error=Failed to delete reminder: " . mysqli_error($conn));
}
exit();
?> 