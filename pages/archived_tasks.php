<?php
/**
 * Archived Tasks Page
 * This file displays the user's archived tasks
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

// Get archived tasks
$query = "SELECT t.*, c.name as category_name 
          FROM tasks t 
          JOIN categories c ON t.category_id = c.category_id 
          WHERE t.user_id = $user_id AND t.is_archived = 1
          ORDER BY t.updated_at DESC";

// Execute query
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Todo List - Archived Tasks</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Todo List</h2>
            <nav>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="add_task.php" class="nav-link">Add New Task</a>
                <a href="archived_tasks.php" class="nav-link active">Archived Tasks</a>
                <a href="reminders.php" class="nav-link">Reminders</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>Archived Tasks</h1>
            
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
            
            <div class="tasks-container">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($task = mysqli_fetch_assoc($result)): ?>
                        <div class="task-card task-priority-<?php echo strtolower($task['priority']); ?>">
                            <div class="task-header">
                                <h3 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                                <span class="task-status status-<?php echo strtolower($task['status']); ?>">
                                    <?php echo $task['status']; ?>
                                </span>
                            </div>
                            
                            <div class="task-description">
                                <?php echo htmlspecialchars($task['description']); ?>
                            </div>
                            
                            <div class="task-meta">
                                <span>Category: <?php echo htmlspecialchars($task['category_name']); ?></span>
                                <span>Priority: <?php echo $task['priority']; ?></span>
                                <span>Due: <?php echo date('M d, Y', strtotime($task['due_date'])); ?></span>
                                <span>Archived on: <?php echo date('M d, Y', strtotime($task['updated_at'])); ?></span>
                            </div>
                            
                            <div class="task-actions">
                                <a href="unarchive_task.php?id=<?php echo $task['task_id']; ?>" class="btn-unarchive">Unarchive</a>
                                <a href="delete_task.php?id=<?php echo $task['task_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to permanently delete this task?')">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-tasks">No archived tasks found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>This is a fictitious website created as part of a university course assignment. All content is for educational purposes only.</p>
    </footer>
</body>
</html> 