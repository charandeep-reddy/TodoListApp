<?php
/**
 * Database Configuration
 * This file contains database connection parameters
 */

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'todo_list_db');

// Attempt to connect to MySQL database
try {
    // Connect to the MySQL server
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
    
    if ($conn === false) {
        throw new Exception("Could not connect to server: " . mysqli_connect_error());
    }
    
    // Check if database exists, if not create it
    $db_check = mysqli_query($conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    
    if (mysqli_num_rows($db_check) == 0) {
        // Database doesn't exist, create it
        $sql = "CREATE DATABASE " . DB_NAME;
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating database: " . mysqli_error($conn));
        }
    }
    
    // Select the database
    if (!mysqli_select_db($conn, DB_NAME)) {
        throw new Exception("Could not select database: " . mysqli_error($conn));
    }
    
    // Set character set
    mysqli_set_charset($conn, "utf8mb4");
    
    // Check if tables exist by attempting to query users table
    $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    
    if (mysqli_num_rows($tables_check) == 0) {
        // Create users table
        $sql = "CREATE TABLE IF NOT EXISTS users (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating users table: " . mysqli_error($conn));
        }
        
        // Create categories table
        $sql = "CREATE TABLE IF NOT EXISTS categories (
                category_id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                description VARCHAR(255)
            )";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating categories table: " . mysqli_error($conn));
        }
        
        // Insert default categories
        $sql = "INSERT INTO categories (name, description) VALUES
                ('Assignment', 'Academic assignments and homework'),
                ('Discussion', 'Group discussions and meetings'),
                ('Club Activity', 'Extracurricular club activities'),
                ('Examination', 'Tests, quizzes, and exams'),
                ('Other', 'Miscellaneous tasks')";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error inserting default categories: " . mysqli_error($conn));
        }
        
        // Create tasks table
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
                task_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(100) NOT NULL,
                description TEXT,
                due_date DATETIME NOT NULL,
                category_id INT NOT NULL,
                priority ENUM('High', 'Medium', 'Low') DEFAULT 'Medium',
                status ENUM('On-going', 'Pending', 'Completed') DEFAULT 'Pending',
                is_archived BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(category_id)
            )";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating tasks table: " . mysqli_error($conn));
        }
        
        // Create reminders table
        $sql = "CREATE TABLE IF NOT EXISTS reminders (
                reminder_id INT AUTO_INCREMENT PRIMARY KEY,
                task_id INT NOT NULL,
                remind_date DATETIME NOT NULL,
                is_notified BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE
            )";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating reminders table: " . mysqli_error($conn));
        }
    }
    
} catch (Exception $e) {
    die("ERROR: Database initialization failed: " . $e->getMessage());
}

// Session start
session_start();
?>
