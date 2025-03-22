<?php
/**
 * Settings Page
 * Allow users to customize application preferences
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user preferences
$query = "SELECT * FROM user_preferences WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0) {
    $preferences = mysqli_fetch_assoc($result);
} else {
    // Default preferences
    $preferences = [
        'theme' => 'light',
        'email_notifications' => 1,
        'default_view' => 'list',
        'tasks_per_page' => 10
    ];
}

// Handle form submission
if(isset($_POST['save_settings'])) {
    $theme = mysqli_real_escape_string($conn, $_POST['theme']);
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $default_view = mysqli_real_escape_string($conn, $_POST['default_view']);
    $tasks_per_page = (int)$_POST['tasks_per_page'];
    
    // Check if preferences exist
    if(mysqli_num_rows($result) > 0) {
        // Update existing preferences
        $update_query = "UPDATE user_preferences SET 
                        theme = '$theme', 
                        email_notifications = $email_notifications, 
                        default_view = '$default_view', 
                        tasks_per_page = $tasks_per_page 
                        WHERE user_id = $user_id";
                        
        if(mysqli_query($conn, $update_query)) {
            $success_message = "Settings saved successfully!";
        } else {
            $error_message = "Error saving settings: " . mysqli_error($conn);
        }
    } else {
        // Insert new preferences
        $insert_query = "INSERT INTO user_preferences (user_id, theme, email_notifications, default_view, tasks_per_page) 
                        VALUES ($user_id, '$theme', $email_notifications, '$default_view', $tasks_per_page)";
                        
        if(mysqli_query($conn, $insert_query)) {
            $success_message = "Settings saved successfully!";
        } else {
            $error_message = "Error saving settings: " . mysqli_error($conn);
        }
    }
    
    // Refresh preferences
    $query = "SELECT * FROM user_preferences WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $preferences = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Student Todo List</title>
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
                <a href="settings.php" class="nav-link active">Settings</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>Settings</h1>
            
            <?php
            // Display success message if any
            if(isset($success_message)) {
                echo '<div class="success-message">' . $success_message . '</div>';
            }
            
            // Display error message if any
            if(isset($error_message)) {
                echo '<div class="error-message">' . $error_message . '</div>';
            }
            ?>
            
            <form method="post" class="settings-form">
                <div class="form-group">
                    <label for="theme">Theme:</label>
                    <select name="theme" id="theme">
                        <option value="light" <?php echo $preferences['theme'] == 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo $preferences['theme'] == 'dark' ? 'selected' : ''; ?>>Dark</option>
                        <option value="blue" <?php echo $preferences['theme'] == 'blue' ? 'selected' : ''; ?>>Blue</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email_notifications">Email Notifications:</label>
                    <input type="checkbox" id="email_notifications" name="email_notifications" <?php echo $preferences['email_notifications'] ? 'checked' : ''; ?>>
                    <span class="checkbox-label">Receive email notifications for upcoming tasks</span>
                </div>
                
                <div class="form-group">
                    <label for="default_view">Default Task View:</label>
                    <select name="default_view" id="default_view">
                        <option value="list" <?php echo $preferences['default_view'] == 'list' ? 'selected' : ''; ?>>List View</option>
                        <option value="grid" <?php echo $preferences['default_view'] == 'grid' ? 'selected' : ''; ?>>Grid View</option>
                        <option value="calendar" <?php echo $preferences['default_view'] == 'calendar' ? 'selected' : ''; ?>>Calendar View</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tasks_per_page">Tasks per page:</label>
                    <select name="tasks_per_page" id="tasks_per_page">
                        <option value="5" <?php echo $preferences['tasks_per_page'] == 5 ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo $preferences['tasks_per_page'] == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?php echo $preferences['tasks_per_page'] == 20 ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?php echo $preferences['tasks_per_page'] == 50 ? 'selected' : ''; ?>>50</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="save_settings">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 