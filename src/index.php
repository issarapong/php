<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 01: PHP พื้นฐาน - หน้าแรก</title>
    <style>
        body { 
            font-family: 'Sarabun', Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header { 
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); 
            color: white; 
            padding: 30px; 
            text-align: center; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 2.5em; 
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p { 
            margin: 10px 0 0 0; 
            font-size: 1.2em; 
            opacity: 0.9;
        }
        .content { 
            padding: 30px; 
        }
        .lab-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin: 30px 0; 
        }
        .lab-card { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            border-radius: 10px; 
            padding: 20px; 
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .lab-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #3498db;
        }
        .lab-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2c3e50);
        }
        .lab-card h3 { 
            color: #2c3e50; 
            margin-top: 0; 
            font-size: 1.3em;
        }
        .lab-card p { 
            color: #666; 
            margin: 10px 0; 
            line-height: 1.6;
        }
        .lab-card a { 
            display: inline-block; 
            background: linear-gradient(135deg, #3498db, #2c3e50); 
            color: white; 
            text-decoration: none; 
            padding: 10px 20px; 
            border-radius: 25px; 
            transition: all 0.3s ease;
            font-weight: bold;
        }
        .lab-card a:hover { 
            background: linear-gradient(135deg, #2980b9, #1a252f);
            transform: scale(1.05);
        }
        .intro-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        .intro-section h2 {
            margin-top: 0;
            font-size: 2em;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .feature-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature-item:hover {
            transform: translateY(-3px);
        }
        .feature-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        .system-info {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .system-info h3 {
            color: #2c3e50;
            margin-top: 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .info-value {
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 PHP Learning Lab</h1>
            <p>การเรียนรู้ PHP ตั้งแต่พื้นฐานไปจนถึงขั้นสูง พร้อม Docker Environment</p>
        </div>

        <div class="content">
            <div class="intro-section">
                <h2>🎯 ยินดีต้อนรับสู่ PHP Lab</h2>
                <p>ระบบการเรียนรู้ PHP ที่ครบครันพร้อมด้วย Docker Environment ที่ประกอบไปด้วย PHP, MySQL, Redis, Memcache และ phpMyAdmin</p>
                
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">🐳</div>
                        <h3>Docker Environment</h3>
                        <p>ระบบที่พร้อมใช้งานทันทีด้วย Docker Compose</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🗄️</div>
                        <h3>Database Integration</h3>
                        <p>MySQL พร้อมข้อมูลตัวอย่างและ phpMyAdmin</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">⚡</div>
                        <h3>Caching Systems</h3>
                        <p>Redis และ Memcache สำหรับ Performance</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">📚</div>
                        <h3>Comprehensive Labs</h3>
                        <p>Lab ครบครันตั้งแต่พื้นฐานถึงขั้นสูง</p>
                    </div>
                </div>
            </div>

            <div class="system-info">
                <h3>ℹ️ ข้อมูลระบบ</h3>
                <div class="info-grid">
                    <?php
                    date_default_timezone_set('Asia/Bangkok');
                    ?>
                    <div class="info-item">
                        <div class="info-label">PHP Version</div>
                        <div class="info-value"><?php echo phpversion(); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Server Software</div>
                        <div class="info-value"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Document Root</div>
                        <div class="info-value"><?php echo $_SERVER['DOCUMENT_ROOT']; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Current Time</div>
                        <div class="info-value"><?php echo date('Y-m-d H:i:s'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Memory Limit</div>
                        <div class="info-value"><?php echo ini_get('memory_limit'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Max Execution Time</div>
                        <div class="info-value"><?php echo ini_get('max_execution_time'); ?> seconds</div>
                    </div>
                </div>
            </div>

            <h2>📖 PHP Labs - เรียงตามลำดับการเรียนรู้</h2>
            
            <div class="lab-grid">
                <div class="lab-card">
                    <h3>🎯 Lab 01: PHP พื้นฐาน</h3>
                    <p>เรียนรู้พื้นฐาน PHP ตั้งแต่ Variables, Arrays, Functions และ Object-Oriented Programming</p>
                    <strong>หัวข้อ:</strong>
                    <ul>
                        <li>Variables และ Data Types</li>
                        <li>Arrays และการจัดการข้อมูล</li>
                        <li>Functions และการเขียนฟังก์ชัน</li>
                        <li>OOP - Classes, Objects, Inheritance</li>
                    </ul>
                    <a href="lab01-php-basics/01-variables.php">เริ่ม Lab 01</a>
                </div>

                <div class="lab-card">
                    <h3>🗄️ Lab 02: Database Management</h3>
                    <p>การเชื่อมต่อและจัดการฐานข้อมูล MySQL ด้วย PDO และ MySQLi</p>
                    <strong>หัวข้อ:</strong>
                    <ul>
                        <li>การเชื่อมต่อฐานข้อมูล</li>
                        <li>CRUD Operations</li>
                        <li>Prepared Statements</li>
                        <li>Error Handling</li>
                    </ul>
                    <a href="lab02-database/">เริ่ม Lab 02</a>
                </div>

                <div class="lab-card">
                    <h3>🚀 Lab 03: Redis Cache</h3>
                    <p>การใช้งาน Redis สำหรับ Caching และ Session Management</p>
                    <strong>หัวข้อ:</strong>
                    <ul>
                        <li>Redis Connection</li>
                        <li>Cache Implementation</li>
                        <li>Session Storage</li>
                        <li>Performance Optimization</li>
                    </ul>
                    <a href="lab03-redis/">เริ่ม Lab 03</a>
                </div>

                <div class="lab-card">
                    <h3>⚡ Lab 04: Memcache</h3>
                    <p>การใช้งาน Memcache สำหรับการจัดการ Cache แบบ Distributed</p>
                    <strong>หัวข้อ:</strong>
                    <ul>
                        <li>Memcache Setup</li>
                        <li>Cache Strategies</li>
                        <li>Data Serialization</li>
                        <li>Cache Expiration</li>
                    </ul>
                    <a href="lab04-memcache/">เริ่ม Lab 04</a>
                </div>

                <div class="lab-card">
                    <h3>🎖️ Lab 05: Advanced Techniques</h3>
                    <p>เทคนิคขั้นสูง API REST, JWT, File Upload และการจัดการ JSON</p>
                    <strong>หัวข้อ:</strong>
                    <ul>
                        <li>RESTful API Development</li>
                        <li>JWT Authentication</li>
                        <li>File Upload & Management</li>
                        <li>JSON Processing</li>
                    </ul>
                    <a href="lab05-advanced/">เริ่ม Lab 05</a>
                </div>
            </div>

            <div class="system-info">
                <h3>🔧 การตั้งค่า Environment</h3>
                <p><strong>Services ที่ใช้งาน:</strong></p>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Web Server</div>
                        <div class="info-value">http://localhost:8080</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">phpMyAdmin</div>
                        <div class="info-value">http://localhost:8081</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">MySQL Database</div>
                        <div class="info-value">localhost:3306</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Redis Cache</div>
                        <div class="info-value">localhost:6379</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Memcache</div>
                        <div class="info-value">localhost:11211</div>
                    </div>
                </div>
                
                <p><strong>Database Credentials:</strong></p>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Database</div>
                        <div class="info-value">php_lab_db</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value">php_user</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Password</div>
                        <div class="info-value">php_password</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Root Password</div>
                        <div class="info-value">root_password</div>
                    </div>
                </div>
            </div>

            <div class="intro-section">
                <h2>🚀 เริ่มต้นใช้งาน</h2>
                <p><strong>การรัน Environment:</strong></p>
                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 15px 0;">
                    <code style="color: white; font-size: 1.1em;">docker-compose up -d</code>
                </div>
                <p>หลังจากรัน Docker Compose แล้ว สามารถเข้าถึง Lab ได้ที่ <strong>http://localhost:8080</strong></p>
            </div>

            <div style="text-align: center; margin-top: 40px; padding: 20px;">
                <p style="color: #666; font-style: italic;">
                    🎓 สร้างโดย PHP Learning Lab - เพื่อการเรียนรู้ที่มีประสิทธิภาพ
                </p>
            </div>
        </div>
    </div>
</body>
</html>