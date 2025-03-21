<?php
/**
 * Reminders Page
 * This file displays the user's reminders
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Get reminders
$query = "SELECT r.*, t.title, t.due_date, t.priority, t.status, c.name as category_name
          FROM reminders r
          JOIN tasks t ON r.task_id = t.task_id
          JOIN categories c ON t.category_id = c.category_id
          WHERE t.user_id = $user_id AND t.is_archived = 0
          ORDER BY r.remind_date ASC";

// Execute query
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Todo List - Reminders</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Todo List</h2>
            <nav>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="add_task.php" class="nav-link">Add New Task</a>
                <a href="archived_tasks.php" class="nav-link">Archived Tasks</a>
                <a href="reminders.php" class="nav-link active">Reminders</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>Reminders</h1>
            
            <?php
            // Display success message if any
            if(isset($_GET['success'])) {
                echo '<div class="success-message">' . htmlspecialchars($_GET['success']) . '</div>';
            }
            
            // Display error message if any
            if(isset($_GET['error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
            
            <div class="reminders-container">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <div class="reminders-list">
                        <?php while($reminder = mysqli_fetch_assoc($result)): ?>
                            <?php 
                            // Determine if reminder is upcoming, today, or overdue
                            $now = new DateTime();
                            $reminder_date = new DateTime($reminder['remind_date']);
                            $today = new DateTime('today');
                            $reminder_status = '';
                            
                            if($reminder_date < $now) {
                                $reminder_status = 'reminder-overdue';
                            } elseif($reminder_date->format('Y-m-d') === $today->format('Y-m-d')) {
                                $reminder_status = 'reminder-today';
                            } else {
                                $reminder_status = 'reminder-upcoming';
                            }
                            ?>
                            
                            <div class="reminder-card <?php echo $reminder_status; ?>">
                                <div class="reminder-header">
                                    <h3 class="reminder-title"><?php echo htmlspecialchars($reminder['title']); ?></h3>
                                    <span class="task-status status-<?php echo strtolower($reminder['status']); ?>">
                                        <?php echo $reminder['status']; ?>
                                    </span>
                                </div>
                                
                                <div class="reminder-meta">
                                    <div>
                                        <span>Category: <?php echo htmlspecialchars($reminder['category_name']); ?></span>
                                        <span>Priority: <?php echo $reminder['priority']; ?></span>
                                    </div>
                                    <div>
                                        <span>Due: <?php echo date('M d, Y', strtotime($reminder['due_date'])); ?></span>
                                        <span>Reminder: <?php echo date('M d, Y - h:i A', strtotime($reminder['remind_date'])); ?></span>
                                    </div>
                                </div>
                                
                                <div class="reminder-actions">
                                    <a href="edit_task.php?id=<?php echo $reminder['task_id']; ?>" class="btn-edit">View Task</a>
                                    <a href="delete_reminder.php?id=<?php echo $reminder['reminder_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this reminder?')">Delete Reminder</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-reminders">No reminders set. Set reminders for your tasks to get notified.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>This is a fictitious website created as part of a university course assignment. All content is for educational purposes only.</p>
    </footer>
</body>
</html> 