# 🏢 Company & Employee Management System

A full-stack application built with **Laravel 12**, **InertiaJS**, **Vue 3**, **Tailwind CSS**, and **Ant Design Vue**.  
This project provides a simple CRUD interface for managing Companies and Employees with authentication using **Laravel Breeze**.

---

## ⚙️ Requirements

- PHP >= 8.2  
- Composer  
- Node.js >= 18  
- NPM or Yarn  
- SQLite (default) or MySQL  

---

## 🛠️ Installation

### 1️⃣ Clone Repository
```bash
git clone https://github.com/yourusername/company-management.git
cd company-management

composer install

npm install

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

php artisan migrate

php artisan db:seed --class=UserSeeder

## 🛠️ Run Apps
npm run dev

php artisan serve

## 🛠️ Build to Prod

npm run build

php artisan storage:link
