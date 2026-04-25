# 🌴 PCA Hybridization Portal System

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com/)
[![Filament Version](https://img.shields.io/badge/Filament-3.2-D97706?style=flat-square&logo=filament)](https://filamentphp.com/)
[![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)](LICENSE)

The **PCA Hybridization Portal System** is a secure, enterprise-grade web application designed for the **Philippine Coconut Authority (PCA)**. It streamlines and centralizes the management of coconut hybridization activities across multiple field sites (Loay and Balilihan Farms), ensuring data integrity, traceability, and professional reporting.

Originally conceptualized as a Django-based system, this modern implementation leverages the **Laravel 11** ecosystem and **Filament v3** for a high-performance, real-time administrative experience.

---

## ✨ Key Features

### 🚜 Field Data Modules
- **Hybrid Seedling Distribution**: Tracks the distribution of seedlings to farmers with detailed location and variety data.
- **Monthly Seednut Harvest**: Manages on-farm hybrid seednut production with automated carry-forward logic.
- **Nursery Operations**: Full lifecycle tracking of communal nurseries, from sowing to dispatch.
- **Pollen Production & Inventory**: Monitors pollen collection, receipt, and weekly utilization across centers.
- **Terminal Reports**: Specialized end-of-cycle reporting for nursery activities.

### 🔐 Advanced Security & RBAC
- **Role-Based Access Control (RBAC)**: Distinct permissions for **Supervisors** (Field entry), **Admins** (Validation), and **Super Admins** (System Governance).
- **Field-Based Data Isolation**: Supervisors only see data related to their assigned farm (Loay or Balilihan).
- **Audit Logging**: Comprehensive tracking of every action (Create, Update, Delete, Export) for full accountability.
- **Submission Workflow**: Multi-stage validation for hybridization records (Draft → Submitted → Validated).

### 📊 Reporting & Analytics
- **Branded Excel Exports**: Generate official PCA-formatted `.xlsx` reports with logos, headers, and signature footers.
- **PDF Report Generation**: Professional landscape reports for field activities and consolidated audits.
- **Interactive Dashboards**: Real-time stats, trend charts, and activity feeds tailored to each user role.

---

## 🛠️ Tech Stack

- **Framework**: [Laravel 11](https://laravel.com/)
- **Admin Panel**: [Filament v3](https://filamentphp.com/)
- **Database**: SQLite (Development) / PostgreSQL (Production)
- **Styling**: Tailwind CSS
- **Reporting**: [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary), [OpenPyXL](https://openpyxl.readthedocs.io/) (via Laravel Excel)
- **Base Starter**: [Kaido Kit](https://github.com/siubie/kaido-kit)

---

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite/MySQL/PostgreSQL

### Step-by-Step Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/MARCAAAAARRON/PCA-Hybrid-Laravel.git
   cd PCA-Hybrid-Laravel
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Update `DB_CONNECTION` and other credentials in your `.env` file.*

4. **Database Migration & Seeding**
   ```bash
   php artisan migrate --seed
   ```

5. **Setup Permissions (Shield)**
   ```bash
   php artisan shield:generate --all
   php artisan shield:super-admin
   ```

6. **Serve the Application**
   ```bash
   php artisan serve
   ```

---

## 📸 Screenshots

*(Add your screenshots here to showcase the beautiful Filament UI)*
- **Dashboard**: High-level overview with efficiency stats.
- **Data Entry**: Clean, responsive forms with batch entry support.
- **Reports**: Examples of the PCA-branded Excel/PDF outputs.

---

## 🤝 Contributing

This project was developed by **Marc Arron** as part of an undergraduate thesis/capstone project. For inquiries or contributions, please contact the repository owner.

---

## 🙏 Acknowledgments

- **Philippine Coconut Authority (PCA)** for providing the domain expertise and requirements.
- **Kaido Kit** for the robust FilamentPHP starter foundation.
- **The Laravel & Filament Communities** for the amazing tools.

---
⭐ *Give a star if this project helped you!*
