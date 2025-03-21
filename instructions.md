# Student To-Do List Web Application - Instructions

This document provides instructions for setting up and running the Student To-Do List web application.

## Setup on Windows with XAMPP

1. **Install XAMPP**
   - Download XAMPP from the official website: https://www.apachefriends.org/
   - Run the installer and follow the installation instructions
   - Install at least Apache, MySQL, and PHP components

2. **Start the services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services by clicking the "Start" buttons next to them

3. **Set up the project**
   - Copy your entire TodoListApp folder to the htdocs directory (typically located at `C:\xampp\htdocs\`)
   - So the path would be `C:\xampp\htdocs\TodoListApp\`

4. **Set up the database**
   - Open your web browser and go to http://localhost/phpmyadmin/
   - Create a new database called "todo_list_db"
   - Click on the new database, then select the "Import" tab
   - Browse and select your `database/db_schema.sql` file
   - Click "Go" to import the database schema

5. **Update database configuration**
   - Open the `config.php` file in the includes folder (`C:\xampp\htdocs\TodoListApp\includes\config.php`)
   - Ensure the database connection details match your setup (usually default values work: localhost, root, no password)

6. **Access the application**
   - Open your web browser
   - Go to http://localhost/TodoListApp/
   - The login page should appear
   - Register a new account and start using the application

## Setup on macOS with MAMP

1. **Install MAMP**
   - Download MAMP from the official website: https://www.mamp.info/
   - Install the application by dragging it to your Applications folder

2. **Start the services**
   - Open MAMP and click "Start Servers"
   - Both Apache and MySQL should start automatically

3. **Set up the project**
   - Copy your entire TodoListApp folder to the htdocs directory (typically located at `/Applications/MAMP/htdocs/`)
   - So the path would be `/Applications/MAMP/htdocs/TodoListApp/`

4. **Set up the database**
   - Open your web browser and go to http://localhost:8888/phpMyAdmin/ (or the port specified in MAMP)
   - Create a new database called "todo_list_db"
   - Click on the new database, then select the "Import" tab
   - Browse and select your `database/db_schema.sql` file
   - Click "Go" to import the database schema

5. **Update database configuration**
   - Open the `config.php` file in the includes folder (`/Applications/MAMP/htdocs/TodoListApp/includes/config.php`)
   - Update the database connection details to match your MAMP setup
   - Typically:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', 'root'); // MAMP default password is 'root'
     define('DB_NAME', 'todo_list_db');
     ```

6. **Access the application**
   - Open your web browser
   - Go to http://localhost:8888/TodoListApp/ (or the port specified in MAMP)
   - The login page should appear
   - Register a new account and start using the application

## Application Features

1. **User Authentication**
   - Register a new user account
   - Login with username and password
   - Update profile information

2. **Task Management**
   - Create new tasks with title, description, due date, category, and priority
   - Edit existing tasks
   - Mark tasks as "On-going," "Pending," or "Completed"
   - Archive completed tasks instead of deleting them
   - Unarchive tasks when needed

3. **Task Prioritization**
   - Assign priority levels (High, Medium, Low) to tasks
   - Sort tasks by priority

4. **Task Categorization**
   - Organize tasks by categories (Assignment, Discussion, Club Activity, Examination, Other)
   - Filter tasks by category

5. **Reminder System**
   - Set reminders for tasks
   - View all reminders in one place
   - Receive visual indicators for upcoming, today's, and overdue reminders

## Troubleshooting

- If you encounter database connection issues, verify your database credentials in the `config.php` file
- Make sure both Apache and MySQL services are running
- Check the Apache and MySQL logs for any error messages
- Ensure that you have imported the database schema correctly
- If you're having permission issues, check the file permissions on your web server

For additional help, please refer to the XAMPP or MAMP documentation. 