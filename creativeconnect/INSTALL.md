# CreativeConnect – Installation & RBAC guide (XAMPP, Windows/Mac)
1. Start **Apache** and **MySQL** in XAMPP.
2. Copy `creativeconnect` into `htdocs` (Mac: `/Applications/XAMPP/xamppfiles/htdocs/`). Final path: `htdocs/creativeconnect/login.php`.
3. Open **http://localhost/creativeconnect/setup.php** (creates DB, tables, accounts, sample data; safe to re-run, resets demo passwords).
4. Open **http://localhost/creativeconnect/login.php**.

| Role | Email | Password |
|---|---|---|
| admin | admin@creativeconnect.test | Admin123! |
| staff | staff1@ / staff2@ / staff3@creativeconnect.test | Staff123! |
| client | client@creativeconnect.test | Client123! |

| Feature | Admin | Staff | Client |
|---|---|---|---|
| Dashboard with stats | all data | assigned only | own only |
| View requests | all | assigned to them | own (by email) |
| Change status | yes | yes (assigned) | no |
| Assign to staff | yes | no | no |
| Comment / feedback | yes | yes | yes |
| Submit new request | – | – | yes |
| Users & roles, activity log | yes | 403 | 403 |
Public visitors can only submit the Contact form. Access is enforced server-side in PHP (not just hidden menus).
