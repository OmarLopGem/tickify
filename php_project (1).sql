-- =============================================
-- Database: php_project
-- Import this file in XAMPP via phpMyAdmin
-- or run: mysql -u root < php_project.sql
-- =============================================

CREATE DATABASE IF NOT EXISTS php_project;
USE php_project;

-- =============================================
-- Tabla: users
-- =============================================
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,
    is_admin   BOOLEAN DEFAULT FALSE,
    active     BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Tabla: tickets
-- =============================================
CREATE TABLE tickets (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    -- Possible values: open, in_progress, on_hold, resolved, closed, cancelled
    status      ENUM('open', 'in_progress', 'on_hold', 'resolved', 'closed', 'cancelled') DEFAULT 'open',
    priority    INT DEFAULT 3 CHECK (priority BETWEEN 1 AND 5),
    created_by  INT NOT NULL,
    assigned_to INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- =============================================
-- Tabla: ticket_comments
-- =============================================
CREATE TABLE ticket_comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id  INT NOT NULL,
    author_id  INT NOT NULL,
    body       TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- =============================================
-- Datos de ejemplo
-- =============================================

INSERT INTO users (name, email, password, is_admin) VALUES
('Daniel',   'daniel@example.com',   'hashed_pwd_1', TRUE),
('Ana',      'ana@example.com',      'hashed_pwd_2', FALSE),
('Carlos',   'carlos@example.com',   'hashed_pwd_3', FALSE),
('Luis',     'luis@example.com',      'hashed_pwd_4', FALSE),
('Maria',    'maria@example.com',    'hashed_pwd_5', FALSE),
('Jorge',    'jorge@example.com',    'hashed_pwd_6', FALSE),
('Sofia',    'sofia@example.com',    'hashed_pwd_7', FALSE),
('Pedro',    'pedro@example.com',    'hashed_pwd_8', FALSE);

INSERT INTO tickets (title, description, status, priority, created_by, assigned_to) VALUES
('Login not working',              'Users can\'t log in on mobile',                 'open',        5, 4, 1),
('Add dark mode',                  'Users requesting dark theme support',            'in_progress', 3, 3, 1),
('Typo on homepage',               'Header says "Welcme" instead of Welcome',       'closed',      1, 4, 1),
('Slow report page',               'Reports page takes 10+ seconds to load',        'open',        4, 1, 1),
('Export to CSV',                  'Export ticket list to CSV file',                 'on_hold',     2, 3, 1),
('Password reset not sending',    'Reset email never arrives',                      'open',        5, 5, 1),
('Dashboard chart broken',        'Pie chart shows 0% for all categories',          'in_progress', 4, 6, 1),
('Update user profile page',      'Allow users to change their avatar',             'open',        2, 7, 1),
('Search not returning results',  'Search bar returns empty on valid queries',       'open',        4, 8, 1),
('Mobile layout broken',          'Sidebar overlaps content on small screens',       'in_progress', 3, 5, 1),
('Add email notifications',       'Notify assignee when ticket is created',          'on_hold',     3, 1, 1),
('Session expires too fast',      'Users get logged out after 5 minutes',            'open',        5, 6, 1),
('Remove legacy API endpoint',    'Deprecated /v1/tickets still accessible',         'resolved',    2, 1, 1),
('Add pagination to ticket list', 'List shows all 500+ tickets at once',             'in_progress', 3, 8, 1),
('File upload fails over 2MB',    'Server returns 413 on larger attachments',        'open',        4, 7, 1),
('Improve error messages',        'Generic "Something went wrong" not helpful',      'open',        2, 5, 1),
('Two-factor authentication',     'Add 2FA support for admin accounts',              'on_hold',     4, 1, 1),
('Duplicate ticket detection',    'Warn users if a similar ticket already exists',   'open',        1, 3, 1),
('Ticket assignment notification','Notify user via email when assigned a ticket',    'cancelled',   2, 4, 1),
('Fix date formatting',           'Dates show as UTC instead of local timezone',     'resolved',    3, 6, 1);

INSERT INTO ticket_comments (ticket_id, author_id, body) VALUES
(1,  1, 'Reproduced the issue on Android. Looking into it.'),
(1,  4, 'Also happening on iOS Safari.'),
(1,  1, 'Found the bug. Missing event listener on the submit button.'),
(2,  2, 'Started working on the color palette for dark mode.'),
(2,  3, 'Please make sure the toggle is accessible from settings.'),
(3,  2, 'Fixed and deployed to production.'),
(4,  1, 'Needs profiling. Likely a missing index on the queries table.'),
(6,  5, 'Checked spam folder, nothing there either.'),
(6,  1, 'SMTP config looks correct. Checking mail server logs.'),
(7,  6, 'Attached a screenshot of the broken chart.'),
(7,  2, 'Data source returns null for new categories. Fixing the query.'),
(9,  8, 'Tried searching "login bug" and got zero results.'),
(9,  3, 'The search index might need a rebuild.'),
(10, 5, 'Happens on screens under 768px width.'),
(10, 6, 'Added a media query fix. Testing now.'),
(12, 6, 'This started after the last deployment on Friday.'),
(12, 1, 'Rolling back the session timeout config change.'),
(14, 8, 'Page freezes when loading the full list.'),
(14, 2, 'Implementing offset-based pagination with 25 items per page.'),
(15, 7, 'Tried uploading a 3MB PDF and got a server error.'),
(17, 1, 'Evaluating TOTP vs SMS-based 2FA options.'),
(20, 6, 'Was using moment.js with UTC. Switched to local timezone formatting.'),
(20, 3, 'Verified the fix. Dates now display correctly.');
