<?php
/**
 * Edit Task Page
 * This file allows users to edit existing tasks
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
$task_query = "SELECT * FROM tasks WHERE task_id = $task_id AND user_id = $user_id";
$task_result = mysqli_query($conn, $task_query);

// Check if task exists and belongs to the user
if(mysqli_num_rows($task_result) != 1) {
    header("Location: dashboard.php?error=Task not found or unauthorized access");
    exit();
}

$task = mysqli_fetch_assoc($task_result);

// Get categories for dropdown
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);

// Check for reminder
$reminder_query = "SELECT * FROM reminders WHERE task_id = $task_id";
$reminder_result = mysqli_query($conn, $reminder_query);
$has_reminder = mysqli_num_rows($reminder_result) > 0;
$reminder = $has_reminder ? mysqli_fetch_assoc($reminder_result) : null;

// Process form submission
if(isset($_POST['update_task'])) {
    // Get form data and sanitize inputs
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $category_id = (int)$_POST['category_id'];
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Set reminder
    $set_reminder = isset($_POST['set_reminder']) ? 1 : 0;
    $remind_date = isset($_POST['remind_date']) ? mysqli_real_escape_string($conn, $_POST['remind_date']) : null;
    
    // Validate inputs
    $errors = array();
    
    if(empty($title)) {
        $errors[] = "Title is required";
    }
    
    if(empty($due_date)) {
        $errors[] = "Due date is required";
    }
    
    if($category_id <= 0) {
        $errors[] = "Category is required";
    }
    
    // If no errors, proceed with updating task
    if(empty($errors)) {
        // Update task in database
        $update_query = "UPDATE tasks 
                        SET title = '$title', description = '$description', due_date = '$due_date', 
                        category_id = $category_id, priority = '$priority', status = '$status'
                        WHERE task_id = $task_id AND user_id = $user_id";
        
        if(mysqli_query($conn, $update_query)) {
            // Handle reminder
            if($set_reminder && !empty($remind_date)) {
                if($has_reminder) {
                    // Update existing reminder
                    $reminder_update = "UPDATE reminders SET remind_date = '$remind_date' WHERE reminder_id = " . $reminder['reminder_id'];
                    mysqli_query($conn, $reminder_update);
                } else {
                    // Create new reminder
                    $reminder_insert = "INSERT INTO reminders (task_id, remind_date) VALUES ($task_id, '$remind_date')";
                    mysqli_query($conn, $reminder_insert);
                }
            } else if(!$set_reminder && $has_reminder) {
                // Delete existing reminder
                $reminder_delete = "DELETE FROM reminders WHERE reminder_id = " . $reminder['reminder_id'];
                mysqli_query($conn, $reminder_delete);
            }
            
            // Redirect to dashboard with success message
            header("Location: dashboard.php?success=Task updated successfully!");
            exit();
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Todo List - Edit Task</title>
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
            <h1>Edit Task</h1>
            
            <?php
            // Display error messages if any
            if(isset($errors) && !empty($errors)) {
                echo '<div class="error-message">';
                foreach($errors as $error) {
                    echo $error . '<br>';
                }
                echo '</div>';
            }
            ?>
            
            <form class="task-form" action="edit_task.php?id=<?php echo $task_id; ?>" method="post">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="due_date">Due Date:</label>
                    <input type="date" id="due_date" name="due_date" value="<?php echo date('Y-m-d', strtotime($task['due_date'])); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category:</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php mysqli_data_seek($categories_result, 0); // Reset result pointer ?>
                        <?php while($category = mysqli_fetch_assoc($categories_result)): ?>
                            <option value="<?php echo $category['category_id']; ?>" <?php echo $task['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="priority">Priority:</label>
                    <select id="priority" name="priority" required>
                        <option value="High" <?php echo $task['priority'] == 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $task['priority'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo $task['priority'] == 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Pending" <?php echo $task['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="On-going" <?php echo $task['status'] == 'On-going' ? 'selected' : ''; ?>>On-going</option>
                        <option value="Completed" <?php echo $task['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="set_reminder" name="set_reminder" <?php echo $has_reminder ? 'checked' : ''; ?>>
                    <label for="set_reminder">Set Reminder</label>
                </div>
                
                <div class="form-group reminder-date" id="reminder_date_group" style="display: <?php echo $has_reminder ? 'block' : 'none'; ?>;">
                    <label for="remind_date">Remind Date:</label>
                    <input type="datetime-local" id="remind_date" name="remind_date" value="<?php echo $has_reminder ? date('Y-m-d\TH:i', strtotime($reminder['remind_date'])) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <button type="submit" name="update_task">Update Task</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>This is a fictitious website created as part of a university course assignment. All content is for educational purposes only.</p>
    </footer>
    
    <script>
        // Show/hide reminder date field based on checkbox
        document.getElementById('set_reminder').addEventListener('change', function() {
            document.getElementById('reminder_date_group').style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html> 