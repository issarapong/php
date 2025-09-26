-- สร้างฐานข้อมูลสำหรับ PHP Lab
CREATE DATABASE IF NOT EXISTS php_lab_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE php_lab_db;

-- ตาราง users สำหรับการทดสอบ
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ตาราง posts สำหรับการทดสอบ relationship
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ตาราง categories สำหรับการทดสอบ
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ตาราง post_categories สำหรับ many-to-many relationship
CREATE TABLE IF NOT EXISTS post_categories (
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ตาราง sessions สำหรับการจัดการ session
CREATE TABLE IF NOT EXISTS sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    session_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ตาราง cache_entries สำหรับการทดสอบ cache
CREATE TABLE IF NOT EXISTS cache_entries (
    cache_key VARCHAR(255) PRIMARY KEY,
    cache_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP
);

-- ข้อมูลตัวอย่าง
INSERT INTO users (username, email, password, full_name) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith');

INSERT INTO categories (name, description) VALUES
('Technology', 'เทคโนโลยีและการพัฒนา'),
('Programming', 'การเขียนโปรแกรม'),
('Database', 'ฐานข้อมูล'),
('Web Development', 'การพัฒนาเว็บไซต์');

INSERT INTO posts (user_id, title, content, status) VALUES
(1, 'การเริ่มต้นใช้งาน PHP', 'บทความเกี่ยวกับการเริ่มต้นเรียนรู้ PHP สำหรับมือใหม่', 'published'),
(2, 'Database Design Patterns', 'รูปแบบการออกแบบฐานข้อมูลที่ดี', 'published'),
(3, 'Modern Web Development', 'เทคนิคการพัฒนาเว็บสมัยใหม่', 'draft');

INSERT INTO post_categories (post_id, category_id) VALUES
(1, 2),
(1, 4),
(2, 3),
(3, 1),
(3, 4);