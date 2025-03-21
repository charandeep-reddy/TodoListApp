<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Todo List - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h1>Student Todo List</h1>
            <h2>Login</h2>
            
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
            
            <form action="pages/login_process.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="login">Login</button>
                </div>
                <p>Don't have an account? <a href="pages/register.php">Register here</a></p>
            </form>
        </div>
    </div>

    <footer>
        <p>This is a fictitious website created as part of a university course assignment. All content is for educational purposes only.</p>
    </footer>
</body>
</html>
