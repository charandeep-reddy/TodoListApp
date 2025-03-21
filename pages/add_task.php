<?php
/**
 * Add Task Page
 * This file allows users to add new tasks
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get categories for dropdown
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);

// Process form submission
if(isset($_POST['add_task'])) {
    // Get form data and sanitize inputs
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $category_id = (int)$_POST['category_id'];
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $user_id = $_SESSION['user_id'];
    
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
    
    // If no errors, proceed with adding task
    if(empty($errors)) {
        // Insert task into database
        $insert_query = "INSERT INTO tasks (user_id, title, description, due_date, category_id, priority, status) 
                        VALUES ($user_id, '$title', '$description', '$due_date', $category_id, '$priority', '$status')";
        
        if(mysqli_query($conn, $insert_query)) {
            // Get the task ID
            $task_id = mysqli_insert_id($conn);
            
            // Add reminder if set
            if($set_reminder && !empty($remind_date)) {
                $reminder_query = "INSERT INTO reminders (task_id, remind_date) VALUES ($task_id, '$remind_date')";
                mysqli_query($conn, $reminder_query);
            }
            
            // Redirect to dashboard with success message
            header("Location: dashboard.php?success=Task added successfully!");
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
    <title>Student Todo List - Add Task</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Todo List</h2>
            <nav>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="add_task.php" class="nav-link active">Add New Task</a>
                <a href="archived_tasks.php" class="nav-link">Archived Tasks</a>
                <a href="reminders.php" class="nav-link">Reminders</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>Add New Task</h1>
            
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
            
            <form class="task-form" action="add_task.php" method="post">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="due_date">Due Date:</label>
                    <input type="date" id="due_date" name="due_date" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category:</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while($category = mysqli_fetch_assoc($categories_result)): ?>
                            <option value="<?php echo $category['category_id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="priority">Priority:</label>
                    <select id="priority" name="priority" required>
                        <option value="High">High</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Pending" selected>Pending</option>
                        <option value="On-going">On-going</option>
                    </select>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="set_reminder" name="set_reminder">
                    <label for="set_reminder">Set Reminder</label>
                </div>
                
                <div class="form-group reminder-date" id="reminder_date_group" style="display: none;">
                    <label for="remind_date">Remind Date:</label>
                    <input type="datetime-local" id="remind_date" name="remind_date">
                </div>
                
                <div class="form-group">
                    <button type="submit" name="add_task">Add Task</button>
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