-- To-Do List Database Schema

-- Create the database
CREATE DATABASE IF NOT EXISTS todo_list_db;
USE todo_list_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create tasks categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255)
);

-- Insert default categories
INSERT INTO categories (name, description) SELECT 'Assignment', 'Academic assignments and homework' FROM DUAL WHERE NOT EXISTS (SELECT * FROM categories WHERE name = 'Assignment');
INSERT INTO categories (name, description) SELECT 'Discussion', 'Group discussions and meetings' FROM DUAL WHERE NOT EXISTS (SELECT * FROM categories WHERE name = 'Discussion');
INSERT INTO categories (name, description) SELECT 'Club Activity', 'Extracurricular club activities' FROM DUAL WHERE NOT EXISTS (SELECT * FROM categories WHERE name = 'Club Activity');
INSERT INTO categories (name, description) SELECT 'Examination', 'Tests, quizzes, and exams' FROM DUAL WHERE NOT EXISTS (SELECT * FROM categories WHERE name = 'Examination');
INSERT INTO categories (name, description) SELECT 'Other', 'Miscellaneous tasks' FROM DUAL WHERE NOT EXISTS (SELECT * FROM categories WHERE name = 'Other');

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
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
);

-- Create reminders table
CREATE TABLE IF NOT EXISTS reminders (
    reminder_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    remind_date DATETIME NOT NULL,
    is_notified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE
);