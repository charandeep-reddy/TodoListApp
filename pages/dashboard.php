<?php
/**
 * Dashboard Page
 * This file displays the user's dashboard with all tasks
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

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'due_date';

// Build query
$query = "SELECT t.*, c.name as category_name 
          FROM tasks t 
          JOIN categories c ON t.category_id = c.category_id 
          WHERE t.user_id = $user_id AND t.is_archived = 0";

// Apply filters
if(!empty($category)) {
    $query .= " AND t.category_id = " . (int)$category;
}
if(!empty($priority)) {
    $query .= " AND t.priority = '" . mysqli_real_escape_string($conn, $priority) . "'";
}
if(!empty($status)) {
    $query .= " AND t.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}

// Apply sorting
switch($sort) {
    case 'title':
        $query .= " ORDER BY t.title ASC";
        break;
    case 'priority':
        $query .= " ORDER BY FIELD(t.priority, 'High', 'Medium', 'Low')";
        break;
    case 'status':
        $query .= " ORDER BY FIELD(t.status, 'Pending', 'On-going', 'Completed')";
        break;
    case 'category':
        $query .= " ORDER BY c.name ASC";
        break;
    case 'due_date':
    default:
        $query .= " ORDER BY t.due_date ASC";
        break;
}

// Execute query
$result = mysqli_query($conn, $query);

// Get categories for filter
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Todo List - Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Todo List</h2>
            <nav>
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="add_task.php" class="nav-link">Add New Task</a>
                <a href="archived_tasks.php" class="nav-link">Archived Tasks</a>
                <a href="reminders.php" class="nav-link">Reminders</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            
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
            
            <div class="filters">
                <form action="dashboard.php" method="get">
                    <h3>Filter Tasks</h3>
                    <div class="filter-container">
                        <div class="filter-item">
                            <label for="category">Category:</label>
                            <select name="category" id="category">
                                <option value="">All Categories</option>
                                <?php while($category_row = mysqli_fetch_assoc($categories_result)): ?>
                                    <option value="<?php echo $category_row['category_id']; ?>" <?php echo $category == $category_row['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category_row['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="priority">Priority:</label>
                            <select name="priority" id="priority">
                                <option value="">All Priorities</option>
                                <option value="High" <?php echo $priority == 'High' ? 'selected' : ''; ?>>High</option>
                                <option value="Medium" <?php echo $priority == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="Low" <?php echo $priority == 'Low' ? 'selected' : ''; ?>>Low</option>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="status">Status:</label>
                            <select name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="On-going" <?php echo $status == 'On-going' ? 'selected' : ''; ?>>On-going</option>
                                <option value="Completed" <?php echo $status == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="sort">Sort By:</label>
                            <select name="sort" id="sort">
                                <option value="due_date" <?php echo $sort == 'due_date' ? 'selected' : ''; ?>>Due Date</option>
                                <option value="title" <?php echo $sort == 'title' ? 'selected' : ''; ?>>Title</option>
                                <option value="priority" <?php echo $sort == 'priority' ? 'selected' : ''; ?>>Priority</option>
                                <option value="status" <?php echo $sort == 'status' ? 'selected' : ''; ?>>Status</option>
                                <option value="category" <?php echo $sort == 'category' ? 'selected' : ''; ?>>Category</option>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <button type="submit">Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="tasks-container">
                <h2>Your Tasks</h2>
                
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($task = mysqli_fetch_assoc($result)): ?>
                        <div class="task-card task-priority-<?php echo strtolower($task['priority']); ?>">
                            <div class="task-header">
                                <h3 class="task-title">
                                    <a href="task_details.php?id=<?php echo $task['task_id']; ?>">
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </a>
                                </h3>
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
                            </div>
                            
                            <div class="task-actions">
                                <a href="edit_task.php?id=<?php echo $task['task_id']; ?>" class="btn-edit">Edit</a>
                                <a href="task_status.php?id=<?php echo $task['task_id']; ?>&status=Completed" class="btn-complete">Mark Complete</a>
                                <a href="archive_task.php?id=<?php echo $task['task_id']; ?>" class="btn-archive">Archive</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-tasks">No tasks found. <a href="add_task.php">Add a new task</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>This is a fictitious website created as part of a university course assignment. All content is for educational purposes only.</p>
    </footer>
</body>
</html> 