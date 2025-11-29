# Quick Start Guide - Lab Management System

## Prerequisites
- PHP 8.1 or higher
- Composer installed
- Node.js 16+ and NPM installed
- MySQL/PostgreSQL database server running

## Step-by-Step Setup

### 1. Install PHP Dependencies
```bash
composer install
```

### 2. Install Node Dependencies
```bash
npm install
```

### 3. Configure Environment
Make sure you have a `.env` file. If not, copy from `.env.example`:
```bash
# Windows PowerShell
Copy-Item .env.example .env

# Or manually create .env file
```

Edit `.env` file and set your database credentials:
```env
APP_NAME="Lab Management System"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lab_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Create Database
Create a MySQL database named `lab_management` (or whatever you set in .env):
```sql
CREATE DATABASE lab_management;
```

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. (Optional) Seed Sample Data
```bash
php artisan db:seed
```

### 8. Build Frontend Assets

**For Development (with hot reload):**
```bash
npm run dev
```
Keep this terminal running in the background.

**For Production:**
```bash
npm run build
```

### 9. Start Development Server
Open a new terminal and run:
```bash
php artisan serve
```

### 10. Access the Application
Open your browser and go to:
```
http://localhost:8000
```

## Creating Your First User

### Option 1: Using Tinker (Recommended)
```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@lab.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

### Option 2: Register via Web Interface
1. Go to http://localhost:8000/register
2. Fill in the registration form
3. Select your role (admin, lab_technician, or receptionist)

## Default Login Credentials (if seeded)
- Email: admin@lab.com
- Password: password
- Role: Admin

## Troubleshooting

### Permission Errors
```bash
# Windows (if using WSL or Git Bash)
chmod -R 775 storage bootstrap/cache

# Or set permissions manually in Windows Explorer
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Connection Issues
- Check your database server is running
- Verify credentials in `.env` file
- Make sure the database exists

### Frontend Assets Not Loading
- Make sure you ran `npm run build` or `npm run dev`
- Check that Vite is running if using `npm run dev`
- Clear browser cache

## Development Workflow

1. **Terminal 1**: Run `npm run dev` (for frontend hot reload)
2. **Terminal 2**: Run `php artisan serve` (for Laravel server)
3. **Browser**: Open http://localhost:8000

## Production Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Run `php artisan view:cache`
5. Run `npm run build`
6. Point your web server to the `public/` directory

