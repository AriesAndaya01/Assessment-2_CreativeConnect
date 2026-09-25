# CreativeConnect PHP and MySQL Backend

This version connects the existing CreativeConnect frontend to a PHP and MySQL backend.

## Features

- Client registration, login and logout
- Password hashing with `password_hash()` and verification with `password_verify()`
- PHP sessions with secure cookie settings and session ID regeneration
- Project-request CRUD: add, view, edit and delete
- Administrator dashboard for viewing all requests and changing their status
- PDO prepared statements for all user-supplied database values
- Server-side and client-side form validation
- CSRF tokens on forms that change data
- Ownership checks so clients can access only their own project requests
- Escaped output to reduce cross-site scripting risk

## Database tables

- `users`: client and administrator accounts
- `project_requests`: project information linked to a user account

## Run with WAMP

1. Start Apache and MySQL in WAMP.
2. Copy the `creativeconnect Front End` folder into `C:\wamp64\www\`.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Import `database.sql`. It creates the `creativeconnect` database and both tables.
5. Check `config/database.php`. The local defaults are:
   - Host: `127.0.0.1`
   - Port: `3306`
   - Database: `creativeconnect`
   - User: `root`
   - Password: blank
6. Open `http://localhost/creativeconnect%20Front%20End/`.

If the MySQL account is different, set the `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` and `DB_PASS` environment variables, or update the local values in `config/database.php`.

## Create an administrator

Open Command Prompt in the project folder and run:

```bash
php scripts/create_admin.php "Site Administrator" admin@example.com "ChangeThisPassword123!"
```

Use your own email address and a strong password. Do not commit a real password to GitHub.

## CRUD locations

| Operation | Page |
| --- | --- |
| Add | `contact.php` |
| View all owned requests | `dashboard.php` |
| View one request | `request.php` |
| Edit | `request_edit.php` |
| Delete | `request_delete.php` |

Administrators use the same dashboard but can see every client's request and change its status.

## Important files

- `config/database.php`: PDO connection
- `includes/bootstrap.php`: session and security setup
- `includes/functions.php`: validation, access checks and helpers
- `database.sql`: database and table creation
- `scripts/create_admin.php`: administrator account setup

## Suggested manual test

1. Register a client account.
2. Log in and add a project request.
3. Confirm the request appears in the dashboard.
4. View and edit it.
5. Log out and confirm the dashboard redirects to login.
6. Log in as an administrator and update the request status.
7. Log in as the client and confirm the new status appears.
8. Delete the request and confirm it no longer appears.
