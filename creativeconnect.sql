-- ============================================================
-- CreativeConnect - MySQL database
-- ICT312 Advanced Web Information Systems
-- Prepared by: Srijana Bhandari (Database, Testing & Progress Report)
--
-- HOW TO RUN
--   Command line:  mysql -u root -p < creativeconnect.sql
--   phpMyAdmin:    Import tab -> choose this file -> Go
--
-- NAMING RULES (please keep these, Alex - the back end must match)
--   * table names are lowercase and plural            e.g. clients, projects
--   * primary key is always  <table_singular>_id      e.g. client_id
--   * foreign key uses the SAME name as the parent PK e.g. projects.client_id
--   * dates/times end in _at or _date                 e.g. created_at, due_date
--   * money columns end in _aud                       e.g. budget_aud
--   * true/false columns start with is_               e.g. is_published
-- ============================================================

DROP DATABASE IF EXISTS creativeconnect;
CREATE DATABASE creativeconnect
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE creativeconnect;


-- ============================================================
-- 1. USERS
-- Everyone who can log in: team members, admins and clients.
-- A client's personal login lives here; their company details
-- live in the clients table.
-- ============================================================
CREATE TABLE users (
  user_id       INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(100)  NOT NULL,
  email         VARCHAR(150)  NOT NULL UNIQUE,
  password_hash VARCHAR(255)  NOT NULL,
  role          ENUM('admin','team_member','client') NOT NULL DEFAULT 'client',
  job_title     VARCHAR(100)  NULL,          -- e.g. "Video & Digital Editor"
  is_active     TINYINT(1)    NOT NULL DEFAULT 1,
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 2. CLIENTS
-- The company or brand behind a client login.
-- ============================================================
CREATE TABLE clients (
  client_id    INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT          NOT NULL UNIQUE,
  company_name VARCHAR(150) NOT NULL,
  phone        VARCHAR(30)  NULL,
  location     VARCHAR(100) NULL,
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_clients_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 3. SERVICES
-- The six services shown on the home page. The "slug" matches
-- the <option value=""> in the contact form exactly.
-- ============================================================
CREATE TABLE services (
  service_id  INT AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(50)  NOT NULL UNIQUE,   -- video-editing, web-development, ...
  name        VARCHAR(100) NOT NULL,
  description VARCHAR(255) NULL,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;


-- ============================================================
-- 4. PACKAGES
-- The three pricing cards on the services page.
-- ============================================================
CREATE TABLE packages (
  package_id        INT AUTO_INCREMENT PRIMARY KEY,
  name              VARCHAR(50)    NOT NULL UNIQUE,
  price_monthly_aud DECIMAL(10,2)  NOT NULL,
  price_yearly_aud  DECIMAL(10,2)  NOT NULL,
  price_note        VARCHAR(150)   NULL,
  is_featured       TINYINT(1)     NOT NULL DEFAULT 0
) ENGINE=InnoDB;


-- ============================================================
-- 5. REQUESTS
-- One row per submission of the contact form. Column names and
-- limits match the validation rules in js/main.js.
-- ============================================================
CREATE TABLE requests (
  request_id   INT AUTO_INCREMENT PRIMARY KEY,
  client_id    INT           NULL,            -- NULL if they were not logged in
  full_name    VARCHAR(100)  NOT NULL,
  email        VARCHAR(150)  NOT NULL,
  company_name VARCHAR(150)  NOT NULL,
  service_id   INT           NOT NULL,
  deadline     DATE          NOT NULL,
  budget_aud   DECIMAL(10,2) NOT NULL,        -- form minimum is 100
  message      TEXT          NOT NULL,        -- form minimum is 20 characters
  status       ENUM('new','reviewed','approved','declined') NOT NULL DEFAULT 'new',
  submitted_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_requests_client
    FOREIGN KEY (client_id) REFERENCES clients(client_id)
    ON DELETE SET NULL,
  CONSTRAINT fk_requests_service
    FOREIGN KEY (service_id) REFERENCES services(service_id),
  CONSTRAINT chk_requests_budget CHECK (budget_aud >= 100)
) ENGINE=InnoDB;


-- ============================================================
-- 6. PROJECTS
-- An approved request becomes a project the client can track.
-- ============================================================
CREATE TABLE projects (
  project_id       INT AUTO_INCREMENT PRIMARY KEY,
  request_id       INT          NULL UNIQUE,  -- the request it came from
  client_id        INT          NOT NULL,
  service_id       INT          NOT NULL,
  package_id       INT          NULL,
  title            VARCHAR(150) NOT NULL,
  status           ENUM('not_started','in_progress','in_review','completed','cancelled')
                   NOT NULL DEFAULT 'not_started',
  progress_percent TINYINT      NOT NULL DEFAULT 0,
  start_date       DATE         NULL,
  due_date         DATE         NULL,
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_projects_request FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE SET NULL,
  CONSTRAINT fk_projects_client  FOREIGN KEY (client_id)  REFERENCES clients(client_id)   ON DELETE CASCADE,
  CONSTRAINT fk_projects_service FOREIGN KEY (service_id) REFERENCES services(service_id),
  CONSTRAINT fk_projects_package FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE SET NULL,
  CONSTRAINT chk_projects_progress CHECK (progress_percent BETWEEN 0 AND 100)
) ENGINE=InnoDB;


-- ============================================================
-- 7. ASSIGNMENTS
-- Which team member is working on which project.
-- A project can have several people; a person can have several
-- projects. The UNIQUE key stops the same person being added twice.
-- ============================================================
CREATE TABLE assignments (
  assignment_id   INT AUTO_INCREMENT PRIMARY KEY,
  project_id      INT          NOT NULL,
  user_id         INT          NOT NULL,
  role_on_project VARCHAR(100) NULL,
  assigned_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_assignments_project FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
  CONSTRAINT fk_assignments_user    FOREIGN KEY (user_id)    REFERENCES users(user_id)       ON DELETE CASCADE,
  CONSTRAINT uq_assignment UNIQUE (project_id, user_id)
) ENGINE=InnoDB;


-- ============================================================
-- 8. PROJECT UPDATES
-- The progress notes the client sees in the portal.
-- ============================================================
CREATE TABLE project_updates (
  update_id        INT AUTO_INCREMENT PRIMARY KEY,
  project_id       INT      NOT NULL,
  user_id          INT      NOT NULL,        -- who posted the update
  note             TEXT     NOT NULL,
  progress_percent TINYINT  NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_updates_project FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
  CONSTRAINT fk_updates_user    FOREIGN KEY (user_id)    REFERENCES users(user_id)
) ENGINE=InnoDB;


-- ============================================================
-- 9. FEEDBACK
-- Client comments and revision requests on a project.
-- ============================================================
CREATE TABLE feedback (
  feedback_id INT AUTO_INCREMENT PRIMARY KEY,
  project_id  INT      NOT NULL,
  client_id   INT      NOT NULL,
  message     TEXT     NOT NULL,
  is_resolved TINYINT(1) NOT NULL DEFAULT 0,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_feedback_project FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
  CONSTRAINT fk_feedback_client  FOREIGN KEY (client_id)  REFERENCES clients(client_id)   ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 10. TESTIMONIALS
-- The star ratings and quotes in the slider on the home page.
-- ============================================================
CREATE TABLE testimonials (
  testimonial_id INT AUTO_INCREMENT PRIMARY KEY,
  client_id      INT        NOT NULL,
  project_id     INT        NULL,
  rating         TINYINT    NOT NULL,
  quote          VARCHAR(500) NOT NULL,
  is_published   TINYINT(1) NOT NULL DEFAULT 0,
  created_at     DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_testimonials_client  FOREIGN KEY (client_id)  REFERENCES clients(client_id)  ON DELETE CASCADE,
  CONSTRAINT fk_testimonials_project FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE SET NULL,
  CONSTRAINT chk_testimonial_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;


-- ============================================================
-- SAMPLE DATA
-- The password_hash values below are PHP password_hash() output
-- for the text "Password123" - use them for demo logins only.
-- ============================================================

-- --- team members and admins -------------------------------
INSERT INTO users (full_name, email, password_hash, role, job_title) VALUES
('Aries Joshua Andaya',  'aries@creativeconnect.example',   '$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'admin',       'Web Developer & System Administrator'),
('Phommaxay Phimmavong', 'phommaxay@creativeconnect.example','$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'team_member', 'UI/UX Designer'),
('Srijana Bhandari',     'srijana@creativeconnect.example',  '$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'team_member', 'Content Creator'),
('Ishika',               'ishika@creativeconnect.example',   '$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'team_member', 'Video & Digital Editor');

-- --- client logins -----------------------------------------
INSERT INTO users (full_name, email, password_hash, role) VALUES
('Maria Santos',  'maria@brightbrew.example',  '$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'client'),
('Daniel Cruz',   'daniel@northsidegym.example','$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'client'),
('Amara Okafor',  'amara@lumenstudio.example', '$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'client'),
('Ella Nguyen',   'ella@petalflorist.example', '$2y$10$e0NRl7bA3Hh0rWq3sT1vQeQ9Xm5VqA0sKbQ4uZ1gJ2nC8pL6yR0dC', 'client');

INSERT INTO clients (user_id, company_name, phone, location) VALUES
(5, 'BrightBrew Coffee',  '+61 400 111 222', 'Sydney, AU'),
(6, 'Northside Gym',      '+61 400 333 444', 'Melbourne, AU'),
(7, 'Lumen Studio',       '+63 917 555 666', 'Manila, PH'),
(8, 'Petal Florist',      '+61 400 777 888', 'Brisbane, AU');

-- --- services (slugs match the contact form) ----------------
INSERT INTO services (slug, name, description) VALUES
('video-editing',   'Video & Digital Editing', 'From raw footage to finished cut.'),
('web-development', 'Web Development',         'Custom sites and apps, built to last.'),
('content-creation','Content Creation',        'Graphics and copy, made for your brand.'),
('graphic-design',  'Graphic Design',          'Logos, brand kits and print assets.'),
('it-support',      'IT Support & Consulting', 'Guidance on tooling, hosting and delivery.'),
('other',           'Other',                   'Anything that does not fit the list above.');

-- --- packages (match the pricing cards) ---------------------
INSERT INTO packages (name, price_monthly_aud, price_yearly_aud, price_note, is_featured) VALUES
('Portfolio',     450.00,  4200.00, 'One deliverable, start to finish.',   0),
('Creative Team', 1600.00, 1350.00, 'Ongoing support for growing brands.', 1),
('Full Delivery', 3200.00, 2700.00, 'Full builds, fully managed.',         0);

-- --- requests from the contact form -------------------------
INSERT INTO requests (client_id, full_name, email, company_name, service_id, deadline, budget_aud, message, status) VALUES
(1, 'Maria Santos', 'maria@brightbrew.example',   'BrightBrew Coffee', 1, '2026-10-15',  1200.00, 'We need a 60 second promo video cut from our cafe footage for Instagram and TikTok.', 'approved'),
(2, 'Daniel Cruz',  'daniel@northsidegym.example','Northside Gym',     2, '2026-11-30',  4500.00, 'A new booking website for our gym with class timetables and member sign up.',        'approved'),
(3, 'Amara Okafor', 'amara@lumenstudio.example',  'Lumen Studio',      3, '2026-10-05',   800.00, 'Monthly social media graphics and captions for our photography studio.',             'reviewed'),
(4, 'Ella Nguyen',  'ella@petalflorist.example',  'Petal Florist',     4, '2026-12-01',   600.00, 'A new logo and packaging labels for our flower delivery boxes.',                     'new');

-- --- projects created from approved requests ----------------
INSERT INTO projects (request_id, client_id, service_id, package_id, title, status, progress_percent, start_date, due_date) VALUES
(1, 1, 1, 1, 'BrightBrew promo video',       'in_review',   80, '2026-09-01', '2026-10-15'),
(2, 2, 2, 3, 'Northside Gym booking website','in_progress', 45, '2026-09-10', '2026-11-30'),
(3, 3, 3, 2, 'Lumen Studio social pack',     'not_started',  0, NULL,         '2026-10-05');

-- --- who is working on what ---------------------------------
INSERT INTO assignments (project_id, user_id, role_on_project) VALUES
(1, 4, 'Lead editor'),
(1, 3, 'Copy and captions'),
(2, 1, 'Developer'),
(2, 2, 'UI/UX designer'),
(3, 3, 'Content creator');

-- --- progress updates shown in the portal -------------------
INSERT INTO project_updates (project_id, user_id, note, progress_percent) VALUES
(1, 4, 'First cut uploaded for review. Music and colour grade still to come.', 60),
(1, 4, 'Colour grade finished. Waiting on client comments.',                   80),
(2, 1, 'Database and sign up pages are working on the test server.',           45);

-- --- client feedback ----------------------------------------
INSERT INTO feedback (project_id, client_id, message, is_resolved) VALUES
(1, 1, 'Love it. Could the logo at the end stay on screen a little longer?', 0),
(2, 2, 'Please use our navy blue instead of black in the header.',           1);

-- --- testimonials for the home page slider ------------------
INSERT INTO testimonials (client_id, project_id, rating, quote, is_published) VALUES
(1, 1, 5, 'Being able to track our video project without chasing emails made the whole process far less stressful.', 1),
(2, 2, 5, 'Clear communication and a real portfolio to review before we committed.',                                 1),
(3, 3, 5, 'Our project stayed on schedule and every update showed up in the portal.',                                1),
(4, NULL, 5, 'From the first enquiry to the final delivery, everything was organised and easy to follow.',           1);


-- ============================================================
-- CHECK QUERIES
-- Run these after importing to prove the relationships work.
-- ============================================================

-- A. Every request with its service and client company
-- SELECT r.request_id, r.full_name, c.company_name, s.name AS service,
--        r.deadline, r.budget_aud, r.status
-- FROM requests r
-- JOIN services s ON s.service_id = r.service_id
-- LEFT JOIN clients c ON c.client_id = r.client_id
-- ORDER BY r.request_id;

-- B. Project status board with the team members assigned
-- SELECT p.title, cl.company_name, p.status, p.progress_percent, p.due_date,
--        GROUP_CONCAT(u.full_name SEPARATOR ', ') AS team
-- FROM projects p
-- JOIN clients cl     ON cl.client_id = p.client_id
-- LEFT JOIN assignments a ON a.project_id = p.project_id
-- LEFT JOIN users u       ON u.user_id = a.user_id
-- GROUP BY p.project_id;

-- C. Testimonials for the home page slider
-- SELECT cl.company_name, t.rating, t.quote
-- FROM testimonials t
-- JOIN clients cl ON cl.client_id = t.client_id
-- WHERE t.is_published = 1;

-- D. Unresolved feedback that still needs action
-- SELECT p.title, f.message, f.created_at
-- FROM feedback f JOIN projects p ON p.project_id = f.project_id
-- WHERE f.is_resolved = 0;
