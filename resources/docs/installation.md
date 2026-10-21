# RJ CMS Installation Guide

This document explains how to install RJ CMS on your own hosting or local server.

## Requirements
*   PHP 8.2 or higher.
*   MySQL 5.7+ or MariaDB 10.3+.
*   Composer installed.
*   Web server (Apache, Nginx, or Laravel Valet).

## Step-by-Step Installation
1.  Upload the RJ CMS files to your server.
2.  Open your terminal in the project directory and run `composer install`.
3.  Open your browser and navigate to `http://your-domain.com/install`.
4.  **Step 1 (Server Check):** The installer will check if your server meets the requirements (PHP version, writable folders).
5.  **Step 2 (Database):** Enter your database host, name, username, and password. The installer will create the `.env` file.
6.  **Step 3 (Admin Account):** Create your primary administrator account (Name, Email, Password).
7.  **Step 4 (Finish):** The installer will run migrations, seed the database with default data (themes, menus, sample pages), and generate an `install.lock` file to prevent reinstalling.
8.  Once finished, you can log in to `/admin` with the credentials you created.
