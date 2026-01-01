# Ekokemika - Modern E-commerce & Corporate Management System

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![AdminLTE](https://img.shields.io/badge/AdminLTE-3.1-4b5563?style=for-the-badge&logo=adminkte)

## Overview

**Ekokemika** is a comprehensive web solution designed to serve as both a high-performance e-commerce platform and a robust internal corporate interactions management system. It facilitates product sales, dealer management, and internal task coordination (Work Orders) within a unified ecosystem.

## Key Features

### 🛒 customer & E-commerce Portal
- **Product Catalog**: Advanced product browsing with categories, brands, and variant support.
- **Cart & Checkout**: Full e-commerce flow including cart management, shipping tracking, and order placement.
- **Customer Accounts**: Order history tracking, profile management, and wishlist functionality.
- **Interactive Content**: Dedicated sections for **Projects** (Du An), **Services** (Dich Vu), and **Careers** (Tuyen Dung).
- **Search**: Global search functionality across the platform.

### 🏢 B2B & Dealer System
- **Dealer Registration**: Self-service registration portal for partners.
- **Partner Management**: Tools to manage and track dealer relationships.

### 🛠 Internal Management (Admin/Staff)
- **Work Order System**: 
    - Full lifecycle management of internal jobs (Work Orders).
    - Task breakdown, scheduling, and assignment.
    - Discussion channels for specific tasks.
    - Quote & Contract generation linked directly to Production/Jobs.
- **Role-Based Access Control**:
    - **Super Admin / Admin**: Full system control.
    - **Staff**: Operational access restricted to assigned duties.
- **Multi-Tenant Architecture**: Support for multiple tenants (Admins, Courts, Customers) for SaaS scalability.
- **Media Library**: Integrated media manager (CKFinder + Custom Library) for handling assets.

## Technology Stack

- **Backend**: Laravel 11.x
- **Frontend (Admin)**: AdminLTE 3.1 (Bootstrap 4 based)
- **Frontend (Public)**: Blade Templates with Vanilla CSS/JS & Livewire components.
- **Database**: MySQL
- **Tooling**: Vite (Assets), Spatie Permissions (RBAC), Intervention Image (Media).

## Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/quanganhbn168/duanekokemika.git
    cd duanekokemika
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database credentials in the `.env` file.*

4.  **Database Migration & Seeding**
    ```bash
    php artisan migrate --seed
    ```

5.  **Run Development Server**
    ```bash
    npm run dev
    php artisan serve
    ```

## Development Modules

- **Work Orders**: `app/Livewire/WorkOrder`
- **Product Management**: `app/Http/Controllers/ProductController`
- **Dealer System**: `app/Http/Controllers/Frontend/DealerRegistrationController`

## License

This project is proprietary software.
