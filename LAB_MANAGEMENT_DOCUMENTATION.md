# Lab Management Application - Complete Documentation

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Technology Stack](#technology-stack)
3. [Database Schema](#database-schema)
4. [Features](#features)
5. [API Endpoints](#api-endpoints)
6. [Installation & Setup](#installation--setup)
7. [Deployment Guide](#deployment-guide)
8. [User Roles & Permissions](#user-roles--permissions)
9. [Workflow](#workflow)

---

## Architecture Overview

### System Architecture
This is a **web-based application** built using Laravel (PHP) framework with the following architecture:

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL/PostgreSQL (configurable)
- **Authentication**: Laravel Sanctum (for API) + Session-based (for web)
- **PDF Generation**: DomPDF (barryvdh/laravel-dompdf)

### Why This Architecture?

1. **Laravel Framework**: 
   - Robust ORM (Eloquent) for database operations
   - Built-in authentication and authorization
   - Excellent for rapid development
   - Strong community support
   - MVC architecture for maintainability

2. **Blade Templates + Tailwind CSS**:
   - Server-side rendering for better SEO and initial load times
   - Tailwind CSS for modern, responsive UI
   - No need for separate frontend build process for basic functionality
   - Easy to extend with Vue/React if needed later

3. **MySQL/PostgreSQL**:
   - Relational database perfect for lab data
   - ACID compliance for data integrity
   - Excellent performance for complex queries
   - Well-supported by Laravel

4. **Laravel Sanctum**:
   - Lightweight API authentication
   - Token-based for mobile/third-party integrations
   - Built into Laravel ecosystem

---

## Technology Stack

### Backend
- **Framework**: Laravel 10.x
- **Language**: PHP 8.1+
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **PDF Library**: barryvdh/laravel-dompdf
- **Authentication**: Laravel Sanctum

### Frontend
- **Templating**: Blade (Laravel)
- **CSS Framework**: Tailwind CSS 3.x
- **JavaScript**: Vanilla JS (can be extended with Alpine.js or Vue.js)
- **Build Tool**: Vite

### Development Tools
- **Package Manager**: Composer (PHP), NPM (Node.js)
- **Version Control**: Git

---

## Database Schema

### Tables Overview

#### 1. `users` (Extended)
- `id` (Primary Key)
- `name`
- `email` (Unique)
- `password` (Hashed)
- `role` (Enum: admin, lab_technician, receptionist)
- `email_verified_at`
- `remember_token`
- `created_at`, `updated_at`

#### 2. `patients`
- `id` (Primary Key)
- `patient_id` (Unique, Auto-generated: PAT000001)
- `name`
- `date_of_birth`
- `age` (Auto-calculated if DOB provided)
- `gender` (Enum: male, female, other)
- `phone`
- `email`
- `address`, `city`, `state`, `postal_code`, `country`
- `doctor_name`
- `doctor_referral`
- `medical_history`
- `allergies`
- `notes`
- `created_at`, `updated_at`, `deleted_at` (Soft Deletes)

#### 3. `test_categories`
- `id` (Primary Key)
- `name` (e.g., "Hematology", "Biochemistry")
- `code` (Unique, e.g., "HEM", "BIO")
- `description`
- `sort_order`
- `is_active` (Boolean)
- `created_at`, `updated_at`, `deleted_at`

#### 4. `tests`
- `id` (Primary Key)
- `test_category_id` (Foreign Key → test_categories)
- `name` (e.g., "Complete Blood Count", "Glucose")
- `code` (Unique)
- `description`
- `price` (Decimal)
- `turnaround_time_hours`
- `is_active` (Boolean)
- `sort_order`
- `created_at`, `updated_at`, `deleted_at`

#### 5. `test_parameters`
- `id` (Primary Key)
- `test_id` (Foreign Key → tests)
- `name` (e.g., "Hemoglobin", "WBC Count")
- `code` (Optional)
- `unit` (e.g., "g/dL", "cells/μL")
- `normal_range_min`, `normal_range_max`
- `critical_low`, `critical_high`
- `gender_specific` (Enum: none, male, female)
- `male_normal_min`, `male_normal_max`
- `female_normal_min`, `female_normal_max`
- `value_type` (Enum: numeric, text, boolean, calculated)
- `calculation_formula` (For calculated values)
- `reference_values` (JSON/text for text values)
- `sort_order`
- `is_active` (Boolean)
- `created_at`, `updated_at`, `deleted_at`

#### 6. `test_results`
- `id` (Primary Key)
- `result_id` (Unique, Auto-generated: RES20241115001)
- `patient_id` (Foreign Key → patients)
- `test_id` (Foreign Key → tests)
- `ordered_by` (Foreign Key → users, nullable)
- `performed_by` (Foreign Key → users, nullable)
- `order_date`
- `sample_collection_date`
- `result_date`
- `status` (Enum: pending, in_progress, completed, cancelled)
- `notes`
- `doctor_remarks`
- `technician_notes`
- `is_abnormal` (Boolean, Auto-calculated)
- `created_at`, `updated_at`, `deleted_at`

#### 7. `test_result_values`
- `id` (Primary Key)
- `test_result_id` (Foreign Key → test_results)
- `test_parameter_id` (Foreign Key → test_parameters)
- `value` (String, stores numeric or text)
- `flag` (Enum: normal, high, low, critical_high, critical_low, Auto-calculated)
- `notes`
- `created_at`, `updated_at`
- Unique constraint on (`test_result_id`, `test_parameter_id`)

### Relationships

```
User (1) ──< (Many) TestResult (ordered_by)
User (1) ──< (Many) TestResult (performed_by)

Patient (1) ──< (Many) TestResult

TestCategory (1) ──< (Many) Test
Test (1) ──< (Many) TestParameter
Test (1) ──< (Many) TestResult

TestResult (1) ──< (Many) TestResultValue
TestParameter (1) ──< (Many) TestResultValue
```

---

## Features

### 1. Patient Management
- ✅ Add new patients with comprehensive information
- ✅ View, edit, delete patients (soft delete)
- ✅ Search patients by name, ID, phone, email
- ✅ Link patients to multiple test reports
- ✅ Auto-generate unique Patient ID (PAT000001)

### 2. Test Categories & Test Definitions
- ✅ Create and manage test categories (Hematology, Biochemistry, etc.)
- ✅ Define individual tests under each category
- ✅ Each test can have multiple parameters
- ✅ Parameters support:
  - Normal ranges (min/max)
  - Critical values (low/high)
  - Gender-specific ranges
  - Different value types (numeric, text, boolean, calculated)
  - Units for each parameter
  - Reference values

### 3. Test Results Entry
- ✅ Create test results for patients
- ✅ **Auto-flagging**: Automatically flags abnormal values (High/Low/Critical)
- ✅ Gender-aware normal range checking
- ✅ Support for calculated values (formula-based)
- ✅ Notes and comments (doctor remarks, technician notes)
- ✅ Status tracking (pending, in_progress, completed, cancelled)

### 4. Reporting
- ✅ Generate PDF reports
- ✅ Include patient information
- ✅ Display test results with reference ranges
- ✅ Show flags for abnormal values
- ✅ Include comments and remarks
- ✅ Lab header/logo support
- ✅ Support multiple test categories per report

### 5. Dashboard & Analytics
- ✅ Statistics overview:
  - Total patients
  - Total tests
  - Today's results
  - Pending results
  - Abnormal results (last 7 days)
- ✅ Recent test results
- ✅ Daily statistics (last 7 days)
- ✅ Test category distribution

### 6. User Roles & Access Control
- ✅ **Admin**: Full access, can delete records
- ✅ **Lab Technician**: Can enter/edit test results, view patients
- ✅ **Receptionist**: Can create patients, order tests, view results
- ✅ Role-based middleware protection

---

## API Endpoints

### Authentication
All API endpoints require Sanctum authentication token.

### Patients API
```
GET    /api/patients              - List all patients (with pagination)
POST   /api/patients             - Create new patient
GET    /api/patients/{id}        - Get patient details
PUT    /api/patients/{id}        - Update patient
DELETE /api/patients/{id}        - Delete patient (admin only)
GET    /api/patients/{id}/test-results - Get patient's test results
```

### Test Categories API
```
GET    /api/test-categories      - List all categories
POST   /api/test-categories      - Create category
GET    /api/test-categories/{id} - Get category details
PUT    /api/test-categories/{id} - Update category
DELETE /api/test-categories/{id} - Delete category (admin only)
```

### Tests API
```
GET    /api/tests                - List all tests
POST   /api/tests               - Create test
GET    /api/tests/{id}           - Get test details
PUT    /api/tests/{id}           - Update test
DELETE /api/tests/{id}           - Delete test (admin only)
GET    /api/tests/{id}/parameters - Get test parameters
```

### Test Results API
```
GET    /api/test-results         - List all results
POST   /api/test-results         - Create test result
GET    /api/test-results/{id}    - Get result details
PUT    /api/test-results/{id}    - Update result
DELETE /api/test-results/{id}    - Delete result (admin only)
```

### Reports API
```
GET    /api/reports/{id}/generate - Generate PDF report
```

---

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js 16+ and NPM
- MySQL 8.0+ or PostgreSQL 13+
- Web server (Apache/Nginx) or PHP built-in server

### Step 1: Clone/Download Project
```bash
cd /path/to/project
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

### Step 3: Install Node Dependencies
```bash
npm install
```

### Step 4: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file:
```env
APP_NAME="Lab Management System"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lab_management
DB_USERNAME=root
DB_PASSWORD=

# Lab Information (for reports)
LAB_NAME="Your Lab Name"
LAB_ADDRESS="Your Lab Address"
LAB_PHONE="+1234567890"
LAB_EMAIL="lab@example.com"
```

### Step 5: Run Migrations
```bash
php artisan migrate
```

### Step 6: Seed Database (Optional)
```bash
php artisan db:seed
```

This will create:
- Default admin user (email: admin@lab.com, password: password)
- Sample test categories
- Sample tests with parameters
- Sample patients

### Step 7: Build Frontend Assets
```bash
npm run build
# Or for development:
npm run dev
```

### Step 8: Start Development Server
```bash
php artisan serve
```

Visit: http://localhost:8000

### Step 9: Create First Admin User
If you didn't seed, create an admin user:
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@lab.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

---

## Deployment Guide

### Production Checklist

1. **Environment Setup**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Generate new `APP_KEY`
   - Configure database credentials

2. **Optimize Application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

3. **Build Assets**
   ```bash
   npm run build
   ```

4. **Database Migration**
   ```bash
   php artisan migrate --force
   ```

5. **Set Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

6. **Web Server Configuration**
   - Point document root to `public/` directory
   - Configure URL rewriting (Apache: mod_rewrite, Nginx: try_files)

### Example Nginx Configuration
```nginx
server {
    listen 80;
    server_name lab.example.com;
    root /path/to/lab/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## User Roles & Permissions

### Admin
- ✅ Full access to all features
- ✅ Can delete patients, tests, categories, results
- ✅ Can manage users
- ✅ Can view all reports and statistics

### Lab Technician
- ✅ Can view patients
- ✅ Can create and edit test results
- ✅ Can mark results as completed
- ✅ Can add technician notes
- ✅ Can view reports
- ❌ Cannot delete records
- ❌ Cannot manage test definitions

### Receptionist
- ✅ Can create and view patients
- ✅ Can order tests (create test results)
- ✅ Can view test results
- ✅ Can view reports
- ❌ Cannot edit test results
- ❌ Cannot delete records
- ❌ Cannot manage test definitions

---

## Workflow

### Typical Workflow

1. **Receptionist** creates a new patient record
2. **Receptionist** orders a test for the patient
3. **Lab Technician** collects sample (updates sample collection date)
4. **Lab Technician** enters test results:
   - System auto-flags abnormal values
   - Technician can add notes
5. **Lab Technician** marks result as completed
6. **Doctor/Receptionist** can add doctor remarks
7. **System** generates PDF report
8. Report is printed/downloaded and given to patient

---

## Additional Features to Consider

### Future Enhancements
1. **Barcode/QR Code Support**: For sample tracking
2. **Email Notifications**: When results are ready
3. **SMS Alerts**: For critical results
4. **Multi-language Support**: i18n
5. **Advanced Analytics**: Charts and graphs
6. **Export to Excel**: Bulk data export
7. **Appointment Scheduling**: For sample collection
8. **Inventory Management**: For lab supplies
9. **Billing Integration**: Payment processing
10. **HL7 Integration**: For lab equipment connectivity

---

## Support & Maintenance

### Logs
- Application logs: `storage/logs/laravel.log`
- Check logs for errors and debugging

### Common Issues

1. **Permission Errors**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

2. **Composer Autoload Issues**
   ```bash
   composer dump-autoload
   ```

3. **Cache Issues**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

## License
This project is open-source and available for use and modification.

---

## Contact & Support
For issues, questions, or contributions, please refer to the project repository.

