<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Todo List - Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="register-form">
            <h1>Student Todo List</h1>
            <h2>Register</h2>
            
            <?php
            // Display error message if any
            if(isset($_GET['error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
            
            <form action="register_process.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="register">Register</button>
                </div>
                <p>Already have an account? <a href="../index.php">Login here</a></p>
            </form>
        </div>
    </div>

    <footer>
        <p>This is a fictitious website created as part of a university course assignment. All content is for educational purposes only.</p>
    </footer>
</body>
</html> 