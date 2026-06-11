# Secure PHP MVC Automation System

## Setup

1. Copy `.env.example` to `.env`.
2. Update database and Gmail IMAP values in `.env`.
3. Create the MySQL database named in `DB_NAME`.
4. Run `php migrate.php`.
5. Open `http://localhost/DTR/public/login`.

The migration creates a default admin:

- Email: `admin@example.com`
- Password: `ChangeMe123!`

Change that password after first login.

## Modules

- Database: PDO connection, environment config, migrations.
- Auth: login, register, logout, password hashing, CSRF, session validation.
- Dashboard: metrics, recent files, recent activity.
- Email: Gmail/IMAP inbox sync through `.env` credentials.
- Excel: XLSX export of activity logs.
- Word: DOCX generation.
- Admin: activity logs and user management with RBAC.

## Security

- Passwords use `password_hash()` and `password_verify()`.
- Database access uses prepared statements through PDO.
- All POST forms include CSRF tokens.
- Sessions use HttpOnly cookies and user-agent validation.
- Admin-only pages enforce RBAC.
- Secrets are read from `.env`; do not commit real credentials.
