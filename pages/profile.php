<?php
/**
 * Profile Page
 * This file displays the user's profile and allows them to update their information
 */

// Include database configuration
require_once('../includes/config.php');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Process form submission
if(isset($_POST['update_profile'])) {
    // Get form data and sanitize inputs
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate inputs
    $errors = array();
    
    // Validate email
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if email is already in use by another user
    if($email != $user['email']) {
        $check_query = "SELECT * FROM users WHERE email = '$email' AND user_id != $user_id";
        $check_result = mysqli_query($conn, $check_query);
        
        if(mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email is already in use by another account";
        }
    }
    
    // If current password is provided, validate it and the new password
    if(!empty($current_password)) {
        // Verify current password
        if(!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        }
        
        // Validate new password
        if(empty($new_password)) {
            $errors[] = "New password is required when changing password";
        } elseif(strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        
        // Validate password confirmation
        if($new_password != $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }
    
    // If no errors, proceed with updating profile
    if(empty($errors)) {
        // Start building the update query
        $update_query = "UPDATE users SET email = '$email'";
        
        // If password is being changed, add it to the query
        if(!empty($current_password) && !empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query .= ", password = '$hashed_password'";
        }
        
        // Finish the query
        $update_query .= " WHERE user_id = $user_id";
        
        // Execute the update
        if(mysqli_query($conn, $update_query)) {
            // Update successful
            $success_message = "Profile updated successfully!";
            
            // Refresh user data
            $result = mysqli_query($conn, $query);
            $user = mysqli_fetch_assoc($result);
        } else {
            $errors[] = "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// Get task statistics
$stats_query = "SELECT 
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
                    SUM(CASE WHEN status = 'On-going' THEN 1 ELSE 0 END) as ongoing_tasks,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_tasks,
                    SUM(CASE WHEN is_archived = 1 THEN 1 ELSE 0 END) as archived_tasks
                FROM tasks 
                WHERE user_id = $user_id";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Todo List - Profile</title>
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
                <a href="profile.php" class="nav-link active">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>User Profile</h1>
            
            <?php
            // Display success message if any
            if(isset($success_message)) {
                echo '<div class="success-message">' . $success_message . '</div>';
            }
            
            // Display error messages if any
            if(isset($errors) && !empty($errors)) {
                echo '<div class="error-message">';
                foreach($errors as $error) {
                    echo $error . '<br>';
                }
                echo '</div>';
            }
            ?>
            
            <div class="profile-container">
                <div class="profile-section">
                    <h2>Account Information</h2>
                    
                    <form class="profile-form" action="profile.php" method="post">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            <small>Username cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password">
                            <small>Only fill this if you want to change your password</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="update_profile">Update Profile</button>
                        </div>
                    </form>
                </div>
                
                <div class="profile-section">
                    <h2>Task Statistics</h2>
                    
                    <div class="stats-container">
                        <div class="stat-item">
                            <span class="stat-label">Total Tasks:</span>
                            <span class="stat-value"><?php echo $stats['total_tasks']; ?></span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-label">Completed Tasks:</span>
                            <span class="stat-value"><?php echo $stats['completed_tasks']; ?></span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-label">Ongoing Tasks:</span>
                            <span class="stat-value"><?php echo $stats['ongoing_tasks']; ?></span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-label">Pending Tasks:</span>
                            <span class="stat-value"><?php echo $stats['pending_tasks']; ?></span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-label">Archived Tasks:</span>
                            <span class="stat-value"><?php echo $stats['archived_tasks']; ?></span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-label">Account Created:</span>
                            <span class="stat-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>This is a fictitious website created as part of a university course assignment. All content is for educational purposes only.</p>
    </footer>
</body>
</html> 