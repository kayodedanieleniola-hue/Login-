# Login System - Setup Guide

## 📋 Overview
This is a complete authentication system with:
- User registration (email/password & Google OAuth)
- Secure login
- Protected dashboard
- Session management
- Password hashing (bcrypt)

---

## 🚀 Quick Start

### Step 1: Database Setup
1. Open your browser and navigate to:
   ```
   http://localhost/your-project/backend/migrate.php
   ```
2. This will create all necessary tables automatically

### Step 2: Configure Database Credentials
Edit `backend/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password'); // Add your MySQL password
define('DB_NAME', 'login_system');
```

### Step 3: Google OAuth Setup (Optional)
1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create a new project
3. Enable Google+ API
4. Create OAuth 2.0 credentials (Web application)
5. Set authorized redirect URIs:
   - `http://localhost`
   - `http://localhost:your_port`
   - `https://yourdomain.com`

6. Copy your Client ID
7. Update in both `registration.html` and `login.html`:
   ```javascript
   client_id: 'YOUR_CLIENT_ID.apps.googleusercontent.com'
   ```

---

## 📁 Project Structure

```
your-project/
├── registration.html          # Sign up page
├── login.html                 # Sign in page
├── admin.html                 # Protected dashboard
├── forgot-password.html       # Password recovery (optional)
├── backend/
│   ├── database.php           # Database connection & configuration
│   ├── register.php           # Email registration handler
│   ├── register_google.php    # Google OAuth handler
│   ├── login.php              # Login authentication
│   └── migrate.php            # Database migration script
└── README.md                  # This file
```

---

## 🔐 Database Schema

### users table
- `id` - User ID
- `email` - Email address (unique)
- `username` - Username (unique)
- `password_hash` - Bcrypt hashed password
- `google_id` - Google OAuth ID
- `profile_picture` - Google profile picture URL
- `full_name` - User's full name
- `signup_method` - 'email' or 'google'
- `is_verified` - Email verification status
- `is_active` - Account status
- `session_token` - Current session token
- `token_expiry` - Token expiration time
- `created_at` - Registration timestamp
- `updated_at` - Last update timestamp

### Additional Tables
- `login_attempts` - Track login attempts for security
- `password_resets` - Password recovery tokens
- `email_verifications` - Email verification tokens

---

## 🔧 Configuration

### Backend Configuration
All backend files use `database.php` for configuration. Update database credentials:

```php
// backend/database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'login_system');
```

### Frontend Configuration
Update API endpoints if needed (currently uses relative paths):
```javascript
// registration.html - line 432
fetch('backend/register.php', { ... })

// login.html - line 160
fetch('backend/login.php', { ... })
```

### Google OAuth
Replace placeholder Client IDs:
```javascript
// registration.html - line 331
client_id: 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com'

// login.html - line 104
client_id: 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com'
```

---

## 🛡️ Security Features

### Password Security
- ✅ Bcrypt hashing (cost 12)
- ✅ Passwords never stored in plain text
- ✅ Constant-time comparison for verification

### Session Management
- ✅ 256-bit random session tokens
- ✅ 24-hour token expiration
- ✅ Secure token storage in localStorage

### Input Validation
- ✅ Email format validation
- ✅ Username character restrictions (alphanumeric + underscore)
- ✅ Password length requirements (6-128 chars)
- ✅ SQL injection prevention (prepared statements)

### Authentication
- ✅ Login with email OR username
- ✅ Google OAuth integration
- ✅ Account status verification
- ✅ Email verification support

---

## 📱 API Endpoints

### Registration
**POST** `/backend/register.php`
```json
{
  "email": "user@example.com",
  "username": "john_doe",
  "password": "SecurePass123",
  "signupMethod": "email"
}
```

Response (201):
```json
{
  "success": true,
  "message": "Account created successfully",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "username": "john_doe"
  }
}
```

### Google Registration
**POST** `/backend/register_google.php`
```json
{
  "email": "user@example.com",
  "name": "John Doe",
  "picture": "https://...",
  "googleId": "123456789",
  "signupMethod": "google"
}
```

### Login
**POST** `/backend/login.php`
```json
{
  "emailOrUsername": "john_doe",
  "password": "SecurePass123"
}
```

Response (200):
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "username": "john_doe",
    "is_verified": true,
    "profile_picture": null
  },
  "token": "abc123...",
  "expiresIn": 86400
}
```

---

## 🧪 Testing

### Test Registration
1. Go to `registration.html`
2. Fill in the form with:
   - Email: `test@example.com`
   - Username: `testuser`
   - Password: `password123`
   - Confirm password: `password123`
   - Check terms
3. Click "Create Account"
4. Should redirect to `admin.html`

### Test Login
1. Go to `login.html`
2. Use email or username
3. Use the password you set during registration
4. Should redirect to `admin.html` with user data displayed

### Test Google Login
1. Click "Sign in with Google"
2. Select a Google account
3. Should create account or login
4. Redirect to `admin.html`

---

## 🐛 Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify credentials in `backend/database.php`
- Check database name matches
- Ensure user has proper permissions

### 404 Not Found (backend files)
- Check file paths are correct
- Ensure `backend/` folder exists
- Verify file names match exactly
- Check relative paths in HTML files

### Google OAuth Not Working
- Verify Client ID is correct
- Check authorized origins in Google Cloud Console
- Ensure callback URL is whitelisted
- Check browser console for errors

### Token Expiration Issues
- Tokens expire after 24 hours
- User needs to login again
- Frontend stores token in `localStorage`
- Check `admin.html` clears expired tokens

### Password Verification Fails
- Ensure password is correct
- Check for extra spaces
- Verify bcrypt is working (`password_verify()`)
- Check PHP version supports bcrypt

---

## 📝 Next Steps

### Recommended Enhancements
1. **Email Verification**
   - Implement email sending (PHPMailer/SwiftMailer)
   - Add verification token validation
   - Send verification link on registration

2. **Password Recovery**
   - Create `forgot-password.html`
   - Implement `backend/forgot-password.php`
   - Use `password_resets` table

3. **Profile Management**
   - Create profile edit page
   - Allow avatar upload
   - Change password functionality

4. **Security Hardening**
   - Implement rate limiting
   - Add CSRF tokens
   - Use HTTPS in production
   - Implement login attempt tracking

5. **Admin Features**
   - User management dashboard
   - Activity logs
   - Email templates
   - Notification system

---

## 📚 File Descriptions

| File | Purpose |
|------|---------|
| `registration.html` | User registration form with validation |
| `login.html` | User login form with email/username support |
| `admin.html` | Protected dashboard for authenticated users |
| `backend/database.php` | Database connection and table creation |
| `backend/register.php` | Email/password registration handler |
| `backend/register_google.php` | Google OAuth registration handler |
| `backend/login.php` | Authentication and session management |
| `backend/migrate.php` | Database schema setup script |

---

## ⚖️ License & Support

This is a starter template. Feel free to modify and extend as needed.

For issues or questions:
1. Check browser console for JavaScript errors
2. Check PHP error logs
3. Verify database connection
4. Review the troubleshooting section above

---

## 🎉 You're All Set!

Your login system is ready to use. Start with:
1. Run `backend/migrate.php` to setup database
2. Update Google Client ID (if using OAuth)
3. Test registration at `registration.html`
4. Test login at `login.html`
5. View dashboard at `admin.html`

Happy coding! 🚀
