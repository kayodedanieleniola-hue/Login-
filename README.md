# Login System

A complete, production-ready authentication system with email/password and Google OAuth login.

## ✨ Features

- 📝 **User Registration** - Email/password with validation
- 🔐 **Secure Login** - Bcrypt password hashing, session tokens
- 🔑 **Google OAuth** - Seamless Google sign-up/sign-in
- 📊 **Protected Dashboard** - User profile and account management
- 🛡️ **Security** - SQL injection prevention, input validation, secure tokens
- 📱 **Responsive Design** - Beautiful UI that works on all devices
- 🎨 **Modern UI** - Smooth animations and intuitive interface

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Modern web browser

### Installation

1. **Clone or download this repository**
2. **Place files in your web directory** (htdocs for XAMPP, www for WAMP)
3. **Update database credentials** in `backend/database.php`
4. **Run database migration** by visiting `backend/migrate.php`
5. **(Optional) Set up Google OAuth** - See SETUP_GUIDE.md

## 📚 Documentation

See [SETUP_GUIDE.md](./SETUP_GUIDE.md) for comprehensive documentation including:
- Detailed setup instructions
- Database schema
- API endpoints
- Configuration options
- Troubleshooting guide

## 📁 Project Structure

```
├── registration.html       # Sign up page
├── login.html              # Sign in page
├── admin.html              # Protected dashboard
├── backend/
│   ├── database.php        # Database connection
│   ├── register.php        # Email registration
│   ├── register_google.php # Google OAuth
│   ├── login.php           # Authentication
│   ├── migrate.php         # Database setup
│   └── config-template.php # Configuration template
├── .htaccess              # Apache configuration
└── SETUP_GUIDE.md         # Full documentation
```

## 🔐 Security Features

✅ **Password Security**
- Bcrypt hashing with cost 12
- Salted and hashed passwords
- Constant-time verification

✅ **Session Management**
- 256-bit random tokens
- 24-hour expiration
- Secure localStorage storage

✅ **Input Validation**
- Email format validation
- Username restrictions
- Password requirements
- SQL injection prevention (prepared statements)

✅ **Authentication**
- Email or username login
- Google OAuth integration
- Account status verification
- Email verification support

## 🧪 Testing

### Test Registration
1. Go to `registration.html`
2. Create account with test credentials
3. Should redirect to `admin.html`

### Test Login
1. Go to `login.html`
2. Login with credentials from registration
3. Should show user profile on dashboard

### Test Google Login
1. Click "Sign in with Google" button
2. Select your Google account
3. Should auto-create account or login
4. Redirect to dashboard with profile data

## 📝 API Endpoints

### POST `/backend/register.php`
Register with email and password

### POST `/backend/register_google.php`
Register or login with Google OAuth

### POST `/backend/login.php`
Authenticate with email/username and password

## 🛠️ Configuration

### Database
Update credentials in `backend/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'login_system');
```

### Google OAuth
1. Get Client ID from [Google Cloud Console](https://console.cloud.google.com)
2. Update in `registration.html` and `login.html`:
```javascript
client_id: 'YOUR_CLIENT_ID.apps.googleusercontent.com'
```

## 📱 Pages

### registration.html
- Email/password registration form
- Google sign-up button
- Input validation
- Error handling

### login.html
- Email/username + password login
- Google sign-in button
- Password validation
- Session token storage

### admin.html
- Protected user dashboard
- User profile display
- Account information
- Security settings
- Logout functionality

## 🐛 Troubleshooting

**Database connection error?**
- Check MySQL is running
- Verify credentials in `database.php`
- Run `backend/migrate.php`

**Google OAuth not working?**
- Verify Client ID is correct
- Check authorized origins in Google Cloud Console

**Can't login after registration?**
- Check password is correct (case-sensitive)
- Try using username instead of email
- Check browser console for errors

See [SETUP_GUIDE.md](./SETUP_GUIDE.md) for more troubleshooting help.

## 🚀 Deployment

For production deployment:

1. **Enable HTTPS** - Use SSL certificate
2. **Update database credentials** - Use environment variables
3. **Set ENVIRONMENT to 'production'** in config
4. **Disable debug mode** - DISPLAY_ERRORS = false
5. **Update CORS headers** - Restrict origins
6. **Use secure cookies** - Set httpOnly and secure flags
7. **Implement rate limiting** - Prevent brute force attacks

## 📈 Next Steps

Enhance your system with:
- Email verification
- Password recovery
- Profile edit functionality
- Admin user management
- Activity logging
- 2-factor authentication
- Advanced security features

## 📄 License

Free to use and modify for your projects.

## 👨‍💻 Author

Created as a complete authentication system template.

## 🙋 Support

For issues or questions, refer to [SETUP_GUIDE.md](./SETUP_GUIDE.md) or check the troubleshooting section.

---

**Ready to get started?** 
1. Update database credentials
2. Visit `backend/migrate.php` to setup database
3. Go to `registration.html` to create your first account
4. Login and view your dashboard!

Happy coding! 🎉
