# Lost & Found Portal

A modern, responsive web application for reporting and finding lost items. Built with PHP, MySQL, and a modern gradient-based UI design.

![Lost & Found Portal](assets/images/logo.svg)

## Features

### For Users
- 🔐 User registration and login
- 📝 Report lost items with details and images
- ✨ Report found items
- 🔍 Search items by keyword, category, location, and date
- 🤝 Automatic matching of lost and found items
- 🔔 Real-time notifications
- 📊 Personal dashboard with statistics

### For Admins
- 👥 User management and verification
- ✅ Item verification
- 🔄 Match approval workflow
- 📈 Dashboard with statistics
- ⚙️ System administration

## Tech Stack

- **Backend:** PHP 8+
- **Database:** MySQL (XAMPP)
- **Frontend:** HTML5, CSS3, TailwindCSS (via CDN)
- **JavaScript:** Vanilla JS with Ajax
- **Email:** Nodemailer (PHPMailer)
- **Auth:** Session-based with bcrypt password hashing

## Installation

### Prerequisites

- XAMPP (or any PHP 8+ and MySQL environment)
- Web browser
- Code editor

### Setup Steps

1. **Clone/Download the project** to your XAMPP htdocs folder:
   ```bash
   cd C:\xampp\htdocs
   # If using git:
   git clone <repository-url> lost-and-found-portal
   # Or copy all files to the folder
   ```

2. **Start XAMPP** and ensure:
   - Apache is running
   - MySQL is running

3. **Create the database:**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Click "Import" tab
   - Select the file `database/schema.sql`
   - Click "Go"

4. **Configure the application:**
   - Open `config/constants.php` to adjust settings if needed
   - Default database: `lost_and_found_db`
   - Default credentials are configured for localhost

5. **Access the application:**
   - Frontend: http://localhost/lost-and-found-portal/
   - Admin Panel: Login with admin credentials

## Default Admin Account

| Field | Value |
|-------|-------|
| Email | admin@lostandfound.com |
| Password | admin@123 |

⚠️ **Important:** Change the admin password after first login!

## Project Structure

```
lost-and-found-portal/
├── config/                 # Configuration files
│   ├── constants.php      # App constants & helpers
│   ├── database.php       # MySQL connection
│   └── mail.php          # Email configuration
├── controllers/           # Business logic
│   ├── AuthController.php
│   ├── ItemController.php
│   ├── MatchController.php
│   ├── NotificationController.php
│   └── AdminController.php
├── models/               # Data access layer
│   ├── User.php
│   ├── Item.php
│   ├── Match.php
│   └── Notification.php
├── views/                # UI pages
│   ├── layouts/         # Header/footer templates
│   ├── auth/            # Login/register pages
│   ├── user/            # User dashboard pages
│   └── admin/            # Admin dashboard pages
├── api/                  # AJAX endpoints
│   ├── auth/            # Auth API
│   ├── admin/           # Admin API
│   └── *.php            # Core API endpoints
├── assets/              # Static assets
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript
│   └── images/         # Images & logos
├── database/            # Database files
│   └── schema.sql      # MySQL schema
├── uploads/             # Uploaded files
│   └── items/          # Item images
├── index.php           # Landing page
└── README.md           # Documentation
```

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Bcrypt hashed password
- `role` - 'user' or 'admin'
- `verified` - Account verification status
- `created_at` - Registration timestamp

### Items Table
- `id` - Primary key
- `title` - Item title
- `description` - Detailed description
- `category` - Item category
- `location` - Where lost/found
- `date` - Date lost/found
- `type` - 'lost' or 'found'
- `status` - 'pending', 'matched', or 'claimed'
- `user_id` - Owner's user ID
- `image_path` - Path to uploaded image
- `verified` - Admin verification status

### Matches Table
- `id` - Primary key
- `lost_item_id` - Reference to lost item
- `found_item_id` - Reference to found item
- `status` - 'pending', 'approved', or 'rejected'
- `similarity_score` - Match percentage

### Notifications Table
- `id` - Primary key
- `user_id` - Recipient's user ID
- `message` - Notification content
- `type` - 'match', 'verification', or 'system'
- `read` - Read status

## API Endpoints

### Authentication
- `POST /api/auth/register.php` - User registration
- `POST /api/auth/login.php` - User/Admin login
- `POST /api/auth/logout.php` - Logout

### Items
- `GET /api/items.php` - Get items
- `POST /api/items.php` - Create item
- `GET /api/search.php` - Search items

### Matches
- `GET /api/matches.php` - Get user's matches
- `POST /api/matches.php` - Create match

### Notifications
- `GET /api/notifications.php` - Get notifications
- `POST /api/notifications.php` - Mark as read

### Admin
- `GET /api/admin/users.php` - Get all users
- `PUT /api/admin/verify-user.php` - Verify user
- `PUT /api/admin/verify-item.php` - Verify item
- `POST /api/admin/approve-match.php` - Approve match
- `POST /api/admin/reject-match.php` - Reject match

## Configuration

### Database Connection
Edit `config/database.php`:
```php
private $host = 'localhost';
private $name = 'lost_and_found_db';
private $user = 'root';
private $pass = '';
```

### Email Configuration
Edit `config/mail.php`:
```php
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');
define('MAIL_ENABLED', true); // Set to false to disable emails
```

## Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS prevention (input sanitization)
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ CSRF token support
- ✅ Input validation

## UI Features

- 🎨 Modern gradient design
- 📱 Fully responsive (mobile-first)
- ✨ Glassmorphism cards
- 🌙 Clean typography with Inter font
- 🎭 Smooth animations
- 🔔 Toast notifications

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Troubleshooting

### Database Connection Error
- Ensure XAMPP MySQL is running
- Check database credentials in `config/database.php`
- Verify the database was created successfully

### 404 Errors
- Ensure mod_rewrite is enabled (for clean URLs)
- Check .htaccess if present

### Images Not Uploading
- Ensure `uploads/items/` directory exists and is writable
- Check PHP upload limits in php.ini

## License

This project is open source and available under the MIT License.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## Support

For support, email support@lostandfound.com or create an issue in the repository.

---

Built with ❤️ for helping people find what they've lost.
