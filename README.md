[README.md](https://github.com/user-attachments/files/33280274/README.md)
# Creative Connect

Creative Connect is a web system where clients submit requests for creative services and staff manage them. It is built with PHP and MySQL.

## Features

- User registration and login
- Role-based access for admin, staff and client accounts
- Request form with validation (name, email, company, service, deadline, budget, message)
- Staff assignment for each request
- Request status updates
- Comments on requests
- Activity logging of important user actions

## Tech stack

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL

## Database

The database has four tables:

| Table | Purpose |
| --- | --- |
| `users` | Admin, staff and client accounts |
| `requests` | Clients' creative-service requests |
| `comments` | Comments added to requests |
| `activity_log` | Important user activities |

### Relationships

| Parent | Child | Relationship |
| --- | --- | --- |
| `users.id` | `requests.assigned_to` | One staff member can be assigned many requests |
| `requests.id` | `comments.request_id` | One request can have many comments |
| `users.id` | `comments.user_id` | One user can write many comments |
| `users.id` | `activity_log.user_id` | One user can have many activity records |

### Form to database field mapping

PHP field names must match the database columns. The only name that differs is `fullName`, which maps to `requests.full_name`.

| Form / PHP name | Database column |
| --- | --- |
| `fullName` | `requests.full_name` |
| `email` | `requests.email` |
| `company` | `requests.company` |
| `service` | `requests.service` |
| `deadline` | `requests.deadline` |
| `budget` | `requests.budget` |
| `message` | `requests.message` |
| `assigned_to` | `requests.assigned_to` |
| `request_id` | `comments.request_id` |
| `user_id` | `comments.user_id` |

## Getting started

### Requirements

- A local PHP and MySQL server, such as XAMPP, MAMP or WAMP
- phpMyAdmin or another MySQL client

### Setup

1. Clone the repository into your web server's root folder (for example, `htdocs` in XAMPP):

   ```bash
   git clone <repository-url> creative-connect
   ```

2. Start Apache and MySQL.
3. Create a new MySQL database (for example, `creative_connect`).
4. Import the project's SQL file into that database. It creates the four tables and loads the sample data.
5. Update the database connection settings in the PHP config file to match your local database name, username and password.
6. Open the site in your browser:

   ```
   http://localhost/creative-connect
   ```

## Sample data

The SQL file includes:

- 5 sample users across the admin, staff and client roles
- 4 creative-service requests with different statuses
- 3 request comments
- 4 activity-log records
- Sample staff assignments
- Verification queries to check the table relationships

All sample accounts use the password `password`. These are for local testing only; change or remove them before any public deployment.

## Testing

Each test case records the expected result, the actual result and a pass/fail outcome. Areas tested so far:

| Area | Result |
| --- | --- |
| Form validation | Pass |
| Request submission | Pass |
| User login | Pass |
| Access permissions | Pass |
| Request assignment | Pass |
| Status changes | Pass |

## Roadmap

- [ ] Full system test on localhost
- [ ] Run all test cases under each user role
- [ ] Capture screenshots of test results
- [ ] Fix minor issues
- [ ] Confirm field names with Alex
- [ ] Team review of all project files

## Team

<!-- Add team member names and roles here -->
