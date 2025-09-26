# PHP Labs ตั้งแต่พื้นฐานไปถึงขั้นสูง 🚀

ชุดการเรียนรู้ PHP ที่ครอบคลุมตั้งแต่พื้นฐานไปจนถึงเทคนิคขั้นสูง พร้อมการใช้งานฐานข้อมูล Redis, Memcache และเทคโนโลยีเสริมต่างๆ ใน Docker Environment

## 📋 สารบัญ

- [การติดตั้งและใช้งาน](#การติดตั้งและใช้งาน)
- [โครงสร้างโปรเจค](#โครงสร้างโปรเจค)
- [Labs Overview](#labs-overview)
- [เทคโนโลยีที่ใช้](#เทคโนโลยีที่ใช้)
- [การเริ่มต้นใช้งาน](#การเริ่มต้นใช้งาน)
- [Troubleshooting](#troubleshooting)

## 🚀 การติดตั้งและใช้งาน

### ความต้องการระบบ

- **Docker** และ **Docker Compose**
- **Git** (สำหรับ clone repository)
- **Web Browser** (สำหรับทดสอบ)
- **Port** ที่ต้องใช้: `8080` (Web), `8081` (phpMyAdmin), `3306` (MySQL), `6379` (Redis), `11211` (Memcached)

### ขั้นตอนการติดตั้ง

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd php
   ```

2. **เริ่มต้น Docker Services**
   ```bash
   docker-compose up -d
   ```

3. **รอให้ Services เริ่มต้นเสร็จสิ้น** (ประมาณ 30-60 วินาที)

4. **เข้าถึงแอปพลิเคชัน**
   - **หน้าหลัก**: http://localhost:8080
   - **phpMyAdmin**: http://localhost:8081

### การตรวจสอบ Services

```bash
# ตรวจสอบสถานะ containers
docker-compose ps

# ดู logs ของ service
docker-compose logs web
docker-compose logs mysql
```

## 📁 โครงสร้างโปรเจค

```
php/
├── docker-compose.yml          # การตั้งค่า Docker services
├── config/
│   └── php.ini                # การตั้งค่า PHP
├── sql/
│   ├── init.sql               # ฐานข้อมูลเริ่มต้น
│   └── lab05-setup.sql        # ตารางเพิ่มเติมสำหรับ Lab 05
├── src/                       # Source code หลัก
│   ├── index.php              # หน้าหลัก
│   ├── lab01-php-basics/      # Lab 01: PHP พื้นฐาน
│   ├── lab02-database/        # Lab 02: การเชื่อมต่อฐานข้อมูล
│   ├── lab03-redis/           # Lab 03: Redis Cache
│   ├── lab04-memcache/        # Lab 04: Memcache
│   └── lab05-advanced/        # Lab 05: เทคนิคขั้นสูง
└── README.md                  # คู่มือใช้งาน (ไฟล์นี้)
```

## 🎯 Labs Overview

### Lab 01: PHP Basics - พื้นฐาน PHP
**📂 Path**: `src/lab01-php-basics/`

- **01-variables.php**: ตัวแปรและ Data Types
- **02-arrays.php**: Arrays และการจัดการข้อมูล
- **03-functions.php**: Functions และ Scope
- **04-oop.php**: Object-Oriented Programming

**เรียนรู้**:
- ตัวแปรและประเภทข้อมูล
- Arrays, Functions, Classes
- Error Handling พื้นฐาน
- Object-Oriented Programming

---

### Lab 02: Database - ฐานข้อมูล
**📂 Path**: `src/lab02-database/`

- **database.php**: ระบบจัดการการเชื่อมต่อฐานข้อมูล
- **models.php**: Models สำหรับจัดการข้อมูล
- **01-database-connection.php**: การทดสอบและใช้งาน

**เรียนรู้**:
- PDO และ MySQLi
- Prepared Statements
- CRUD Operations
- Singleton Pattern
- Transaction Management

---

### Lab 03: Redis Cache - แคชด้วย Redis
**📂 Path**: `src/lab03-redis/`

- **redis_manager.php**: ระบบจัดการ Redis
- **01-redis-basics.php**: การใช้งานและทดสอบ Redis

**เรียนรู้**:
- Redis Connection และ Commands
- Caching Strategies
- Session Management
- Performance Optimization
- Data Structures (String, Hash, List, Set)

---

### Lab 04: Memcache - การแคชแบบกระจาย
**📂 Path**: `src/lab04-memcache/`

- **memcache_manager.php**: ระบบจัดการ Memcache
- **01-memcache-basics.php**: การใช้งานและทดสอบ Memcache

**เรียนรู้**:
- Memcache/Memcached Setup
- Distributed Caching
- Cache-aside Pattern
- Performance Testing
- Consistent Hashing

---

### Lab 05: Advanced Techniques - เทคนิคขั้นสูง
**📂 Path**: `src/lab05-advanced/`

- **advanced_helpers.php**: Helper Classes (JWT, FileUpload, Validation)
- **api.php**: REST API Endpoints
- **01-advanced-api.php**: การทดสอบและใช้งาน API

**เรียนรู้**:
- RESTful API Development
- JWT Authentication
- File Upload Management
- Input Validation
- JSON Processing
- CORS Handling

## 🛠️ เทคโนโลยีที่ใช้

### Backend Stack
- **PHP 8.2** with Apache
- **MySQL 8.0** - ฐานข้อมูลหลัก
- **Redis 7** - Caching และ Session
- **Memcached 1.6** - Distributed Caching

### PHP Extensions
- `mysqli`, `pdo_mysql` - Database connectivity
- `redis` - Redis client
- `memcached` - Memcached client
- `gd` - Image processing
- `curl` - HTTP requests
- `json` - JSON processing

### Development Tools
- **phpMyAdmin** - Database management
- **Docker Compose** - Container orchestration

## 🚦 การเริ่มต้นใช้งาน

### 1. เริ่มต้นใช้งาน

```bash
# Clone และเข้าไปในโฟลเดอร์
git clone <repository-url>
cd php

# เริ่มต้น services
docker-compose up -d

# ตรวจสอบสถานะ
docker-compose ps
```

### 2. เข้าใช้งานแอปพลิเคชัน

- **หน้าหลัก**: http://localhost:8080
- ไปที่ Lab ที่ต้องการเรียนรู้
- ทำตามขั้นตอนในแต่ละ Lab

### 3. การใช้งาน phpMyAdmin

- URL: http://localhost:8081
- Username: `root`
- Password: `rootpassword`
- Database: `lab_database`

### 4. Test Users สำหรับ Lab 05

```json
{
  "username": "testuser",
  "password": "password",
  "email": "test@example.com"
}
```

```json
{
  "username": "apiuser", 
  "password": "password",
  "email": "api@example.com"
}
```

## 🔧 การใช้งานแต่ละ Service

### MySQL Database
```php
// ตัวอย่างการเชื่อมต่อ
$database = Database::getInstance();
$pdo = $database->getConnection();
```

### Redis Cache
```php
// ตัวอย่างการใช้งาน
$redis = RedisManager::getInstance();
$redis->set('key', 'value');
$value = $redis->get('key');
```

### Memcache
```php
// ตัวอย่างการใช้งาน
$memcache = MemcacheManager::getInstance();
$memcache->set('key', 'value', 300);
$value = $memcache->get('key');
```

### REST API (Lab 05)
```bash
# ลงทะเบียนผู้ใช้
curl -X POST http://localhost:8080/lab05-advanced/api.php/auth/register \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","email":"test@example.com","password":"password123"}'

# เข้าสู่ระบบ
curl -X POST http://localhost:8080/lab05-advanced/api.php/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","password":"password123"}'

# ดึงข้อมูลโพสต์
curl -X GET http://localhost:8080/lab05-advanced/api.php/posts

# สร้างโพสต์ใหม่ (ต้องมี token)
curl -X POST http://localhost:8080/lab05-advanced/api.php/posts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{"title":"Test Post","content":"This is a test post"}'
```

## 🐛 Troubleshooting

### ปัญหาที่พบบ่อย

#### 1. **Port Already in Use**
```bash
# ตรวจสอบ ports ที่ใช้
netstat -tulpn | grep :8080

# หยุด containers และเริ่มใหม่
docker-compose down
docker-compose up -d
```

#### 2. **MySQL Connection Error**
```bash
# ตรวจสอบ logs ของ MySQL
docker-compose logs mysql

# รีสตาร์ต MySQL service
docker-compose restart mysql
```

#### 3. **Redis/Memcache Connection Error**
```bash
# ตรวจสอบว่า services ทำงาน
docker-compose ps

# รีสตาร์ต services
docker-compose restart redis memcached
```

#### 4. **File Upload Permission Error**
```bash
# ตรวจสอบและแก้ไข permissions
docker-compose exec web chown -R www-data:www-data /var/www/html/lab05-advanced/uploads/
docker-compose exec web chmod -R 755 /var/www/html/lab05-advanced/uploads/
```

#### 5. **PHP Extension Not Found**
```bash
# ตรวจสอบ PHP extensions
docker-compose exec web php -m | grep redis
docker-compose exec web php -m | grep memcached

# หากไม่มี extension ให้ rebuild image
docker-compose build --no-cache web
```

### การ Debug

#### 1. **ดู PHP Error Logs**
```bash
docker-compose logs web
```

#### 2. **ตรวจสอบ Database Schema**
```sql
-- เชื่อมต่อผ่าน phpMyAdmin หรือ MySQL client
SHOW TABLES;
DESCRIBE users;
DESCRIBE posts;
DESCRIBE uploaded_files;
```

#### 3. **ทดสอบการเชื่อมต่อ Services**
```php
// ทดสอบใน PHP
<?php
// Test Redis
try {
    $redis = new Redis();
    $redis->connect('redis', 6379);
    echo "Redis: Connected\n";
} catch (Exception $e) {
    echo "Redis Error: " . $e->getMessage() . "\n";
}

// Test Memcache
try {
    $memcache = new Memcached();
    $memcache->addServer('memcached', 11211);
    echo "Memcached: Connected\n";
} catch (Exception $e) {
    echo "Memcached Error: " . $e->getMessage() . "\n";
}
?>
```

## 📚 เอกสารเพิ่มเติม

### Official Documentation
- [PHP Manual](https://www.php.net/manual/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Redis Documentation](https://redis.io/documentation)
- [Memcached Wiki](https://github.com/memcached/memcached/wiki)

### Learning Resources
- [PHP The Right Way](https://phptherightway.com/)
- [REST API Best Practices](https://restfulapi.net/)
- [JWT Introduction](https://jwt.io/introduction/)

## 🤝 การมีส่วนร่วม

หากพบปัญหาหรือต้องการปรับปรุง:

1. สร้าง Issue ใน Repository
2. ส่ง Pull Request พร้อมการอธิบาย
3. เปิด Discussion สำหรับข้อเสนอแนะ

## 📄 License

โปรเจคนี้ใช้สำหรับการศึกษาและเรียนรู้ สามารถนำไปใช้และแก้ไขได้อย่างเสรี

---

## 🎉 สรุป

Labs เหล่านี้ครอบคลุม:

- ✅ **PHP พื้นฐาน**: Variables, Functions, OOP
- ✅ **Database**: PDO, MySQL, CRUD Operations  
- ✅ **Caching**: Redis และ Memcache
- ✅ **Advanced**: REST API, JWT, File Upload, JSON

เหมาะสำหรับ:
- 👨‍🎓 นักเรียน/นักศึกษา ที่ต้องการเรียนรู้ PHP
- 👨‍💻 Developer ที่ต้องการทบทวน PHP skills
- 🏢 องค์กร ที่ต้องการ training materials

**Happy Coding!** 🚀