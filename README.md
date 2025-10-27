🗂️ Kanban Task Management System

Kanban Reporting System built with Laravel, Inertia.js, and React.
This project allows users to create projects, manage tasks, and organize work efficiently using a drag-and-drop interface.

🚀 Features

Create and manage projects

Add, edit, and delete tasks

Drag and drop tasks between columns (Pending, In Progress, Completed)

Task modals for viewing and updating task details

Built with Laravel 11, Inertia.js, React, and Tailwind CSS

Uses SQLite (in-memory) for testing and MySQL (default) for development

🧰 Tech Stack
Category	Tools / Frameworks
Backend	Laravel 11
Frontend	React + Inertia.js
Styling	Tailwind CSS
Database	MySQL / SQLite
Build Tool	Vite
Package Manager	NPM
⚙️ Prerequisites

Ensure you have the following installed on your machine:

PHP ≥ 8.2

Composer

Node.js ≥ 18 and NPM

MySQL (or SQLite)

🧑‍💻 Installation & Setup
Step 1 — Clone the Repository
git clone https://github.com/norbertose09/kanban-reporting-system.git
cd kanban-reporting-system

Step 2 — Install PHP Dependencies
composer install

Step 3 — Install JavaScript Dependencies
npm install

Step 4 — Configure Environment

Copy the example environment file:

cp .env.example .env


Update your .env file with your preferred database settings:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kanban_db
DB_USERNAME=root
DB_PASSWORD=


Then generate your app key:

php artisan key:generate

Step 5 — Run Database Migrations
php artisan migrate --seed

Step 6 — Start the Laravel Development Server
php artisan serve

Step 7 — Start the React (Vite) Development Server

Open another terminal and run:

npm run dev

Step 8 — Visit the App

Open your browser and navigate to:

http://localhost:8000

🧪 Running Tests

To run all tests:

php artisan test


Laravel automatically loads .env.testing for the test environment.

🪄 Available Commands
Command	Description
php artisan serve	Starts Laravel backend server
npm run dev	Runs React frontend with Vite
npm run build	Builds production assets
php artisan test	Runs PHPUnit tests
🧾 Example .env Configuration
APP_NAME=Kanban
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kanban_db
DB_USERNAME=root
DB_PASSWORD=


Run:

php artisan key:generate


to generate a valid APP_KEY.

👤 Author

Norbert Madojemu
💻 Full Stack Developer — Laravel | React | Next.js
📧 norbertose09@gmail.com

🔗 https://github.com/norbertose09
