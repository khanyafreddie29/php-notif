create database if not exists notif_db;
use notif_db;

create table employees(
	employee_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(500) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL
);

create table notification_records(
	notification_id INT AUTO_INCREMENT PRIMARY KEY UNIQUE NOT NULL,
    employee_id INT NULL,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    is_broadcast TINYINT NOT NULL DEFAULT '0'
);

INSERT INTO employees (name, last_name, email, username, password)
VALUES 
('emily', 'smith', 'emilytest@gmail.com', 'emilytest01', 'myemily01'),
('sarah', 'king', 'sarah02@gmail.com', 'sarahtest02', 'mysarah02');

-- Update existing notifications to be employee-specific
UPDATE notification_records SET employee_id = 1 WHERE notification_id = 1;
UPDATE notification_records SET employee_id = 2 WHERE notification_id = 2;

-- Add some broadcast notifications (for all employees)
INSERT INTO notification_records (employee_id, title, message, date_created, is_broadcast) VALUES
(NULL, 'System Update', 'New features added to the attendance system!', NOW(), 1),
(NULL,'Holiday Notice', 'Office will be closed on December 25th', NOW(), 1),
(1,'Sign In Reminder', 'please do not forget to visit admin for late coming', NOW(), 0),
(2, 'Clock Out Reminder', 'please do not forget to clock out before leaving', NOW(), 0),
(NULL, 'Aunt Shirley Shop','aunty shirley is here with er regular meals. please take the time to support her shop.', NOW(),1);