# Task Manager

Task Manager is a web application built with Laravel 12 that allows users to manage tasks efficiently. It includes user authentication, task creation, updating, assignment, and filtering by status, priority, and assignee.

## Features

- User registration and login
- Dashboard displaying tasks with filters by status, priority, assigned user, and date
- Create, update, and delete tasks
- Upload images for tasks
- Assign tasks to users
- Role and permission management using Spatie Laravel Permission package

## Requirements

- PHP 8.2 or higher
- Composer
- Laravel 12
- A database supported by Laravel (e.g., MySQL, SQLite, PostgreSQL)

## Installation

1. Clone the repository:

   ```bash
   git clone <repository-url>
   cd Task-Manager
   ```

2. Install PHP dependencies using Composer:

   ```bash
   composer install
   ```

3. Copy the example environment file and configure your environment variables:

   ```bash
   cp .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Configure your database settings in the `.env` file.

6. Run database migrations and seeders:

   ```bash
   php artisan migrate --seed
   ```

7. (Optional) Create an admin user using the provided artisan command:

   ```bash
   php artisan createAdmin
   ```

8. Install Node.js dependencies and build assets:

   ```bash
   npm install
   npm run dev
   ```

## Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser. You will be redirected to the login page.

## Usage

- Register a new user or login with existing credentials.
- Access the dashboard to view tasks.
- Create new tasks with title, description, status, priority, and optional image.
- Update task details and status.
- Assign tasks to users.
- Filter tasks by status, priority, assigned user, and date.

## Technologies Used

- Laravel 12
- PHP 8.2
- Spatie Laravel Permission for roles and permissions
- Blade templating engine
- MySQL or other supported databases
- Node.js, npm, and Vite for frontend assets

## License

This project is licensed under the MIT License.
