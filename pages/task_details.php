<?php
/**
 * Task Details Page
 * Shows comprehensive details about a single task
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

// Get task details
$query = "SELECT t.*, c.name as category_name 
          FROM tasks t 
          LEFT JOIN categories c ON t.category_id = c.category_id 
          WHERE t.task_id = $task_id AND t.user_id = $user_id";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) != 1) {
    header("Location: dashboard.php?error=Task not found or unauthorized access");
    exit();
}

$task = mysqli_fetch_assoc($result);

// Get comments/notes for this task
$comments_query = "SELECT * FROM task_comments WHERE task_id = $task_id ORDER BY created_at DESC";
$comments_result = mysqli_query($conn, $comments_query);

// Page title
$page_title = "Task Details: " . htmlspecialchars($task['title']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Student Todo List</title>
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
                <a href="reminders.php" class="nav-link">Reminders</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>Task Details</h1>
            
            <div class="task-details">
                <div class="task-header">
                    <h2><?php echo htmlspecialchars($task['title']); ?></h2>
                    <div class="task-status priority-<?php echo strtolower($task['priority']); ?>">
                        <?php echo $task['priority']; ?> Priority
                    </div>
                </div>
                
                <div class="task-metadata">
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($task['category_name'] ?? 'None'); ?></p>
                    <p><strong>Due Date:</strong> <?php echo date('F j, Y', strtotime($task['due_date'])); ?></p>
                    <p><strong>Status:</strong> <?php echo $task['is_completed'] ? 'Completed' : 'Pending'; ?></p>
                    <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime($task['created_at'])); ?></p>
                </div>
                
                <div class="task-content">
                    <h3>Description</h3>
                    <div class="task-description">
                        <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                    </div>
                </div>
                
                <div class="task-actions">
                    <a href="edit_task.php?id=<?php echo $task_id; ?>" class="btn-edit">Edit Task</a>
                    <?php if($task['is_completed'] == 0): ?>
                        <a href="task_status.php?id=<?php echo $task_id; ?>&status=1" class="btn-complete">Mark as Complete</a>
                    <?php else: ?>
                        <a href="task_status.php?id=<?php echo $task_id; ?>&status=0" class="btn-incomplete">Mark as Incomplete</a>
                    <?php endif; ?>
                    
                    <?php if($task['is_archived'] == 0): ?>
                        <a href="archive_task.php?id=<?php echo $task_id; ?>" class="btn-archive">Archive Task</a>
                    <?php else: ?>
                        <a href="unarchive_task.php?id=<?php echo $task_id; ?>" class="btn-unarchive">Unarchive Task</a>
                    <?php endif; ?>
                    
                    <a href="delete_task.php?id=<?php echo $task_id; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this task?')">Delete Task</a>
                </div>
                
                <div class="task-comments">
                    <h3>Notes & Comments</h3>
                    
                    <form action="add_comment.php" method="post" class="comment-form">
                        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                        <textarea name="comment" placeholder="Add a note or comment..." required></textarea>
                        <button type="submit" name="add_comment">Add Note</button>
                    </form>
                    
                    <div class="comments-list">
                        <?php if(mysqli_num_rows($comments_result) > 0): ?>
                            <?php while($comment = mysqli_fetch_assoc($comments_result)): ?>
                                <div class="comment-item">
                                    <div class="comment-date"><?php echo date('M j, Y g:i a', strtotime($comment['created_at'])); ?></div>
                                    <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="no-comments">No notes or comments yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 