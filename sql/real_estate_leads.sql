-- Create database
CREATE DATABASE IF NOT EXISTS real_estate_leads;
USE real_estate_leads;

-- Lead Status Table (must be created before referencing in leads)
CREATE TABLE lead_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) NOT NULL
);

-- Users Table (Admins & Employees)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Leads Table
CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    property_interest VARCHAR(255),
    budget VARCHAR(50),
    source VARCHAR(100),
    notes TEXT,
    status_id INT DEFAULT 1,
    assigned_to INT DEFAULT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (status_id) REFERENCES lead_status(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default lead statuses
INSERT INTO lead_status (status_name) VALUES 
('New'), 
('Contacted'), 
('Site Visit'), 
('Closed'), 
('Not Interested');

-- Lead Follow-ups Table
CREATE TABLE lead_followups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    user_id INT NOT NULL,
    note TEXT,
    followup_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
