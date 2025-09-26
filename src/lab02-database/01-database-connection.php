<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 02: Database Management - การเชื่อมต่อฐานข้อมูล</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #e67e22; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background-color: #d5f4e6; border-left: 4px solid #27ae60; padding: 15px; margin: 10px 0; }
        .error { background-color: #fadbd8; border-left: 4px solid #e74c3c; padding: 15px; margin: 10px 0; }
        .info { background-color: #d5dbdb; border-left: 4px solid #3498db; padding: 15px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #bdc3c7; padding: 8px; text-align: left; }
        th { background-color: #e67e22; color: white; }
        .btn { display: inline-block; padding: 8px 15px; background: #e67e22; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #d35400; }
        .status-published { color: #27ae60; font-weight: bold; }
        .status-draft { color: #f39c12; font-weight: bold; }
        .status-archived { color: #95a5a6; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 02: Database Management - การเชื่อมต่อฐานข้อมูล</h1>

        <?php
        require_once 'models.php';

        // ทดสอบการเชื่อมต่อฐานข้อมูล
        echo "<div class='lab-section'>";
        echo "<h2>1. การทดสอบการเชื่อมต่อฐานข้อมูล</h2>";
        
        try {
            $db = Database::getInstance()->getConnection();
            echo "<div class='success'>";
            echo "<h3>✅ เชื่อมต่อฐานข้อมูลสำเร็จ!</h3>";
            echo "<p><strong>Host:</strong> " . DatabaseConfig::HOST . "</p>";
            echo "<p><strong>Database:</strong> " . DatabaseConfig::DB_NAME . "</p>";
            echo "<p><strong>Username:</strong> " . DatabaseConfig::USERNAME . "</p>";
            echo "<p><strong>Character Set:</strong> " . DatabaseConfig::CHARSET . "</p>";
            echo "</div>";
            
            // ทดสอบ query พื้นฐาน
            $stmt = $db->query("SELECT VERSION() as mysql_version");
            $version = $stmt->fetch();
            echo "<div class='info'>";
            echo "<p><strong>MySQL Version:</strong> " . $version['mysql_version'] . "</p>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<h3>❌ การเชื่อมต่อฐานข้อมูลล้มเหลว!</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "</div>";
        }
        echo "</div>";
        ?>

        <div class="lab-section">
            <h2>2. การจัดการผู้ใช้ (User Management)</h2>
            <div class="output">
                <?php
                echo "<h3>2.1 การแสดงรายชื่อผู้ใช้ทั้งหมด</h3>";
                
                $userModel = new User();
                $users = $userModel->getAllWithoutPassword();
                
                if (!empty($users)) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>ชื่อเต็ม</th><th>วันที่สร้าง</th></tr>";
                    
                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . $user['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($user['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    echo "<p><strong>จำนวนผู้ใช้ทั้งหมด:</strong> " . count($users) . " คน</p>";
                } else {
                    echo "<p>ไม่พบข้อมูลผู้ใช้</p>";
                }

                echo "<h3>2.2 การสร้างผู้ใช้ใหม่</h3>";
                
                // ทดสอบสร้างผู้ใช้ใหม่
                $newUserData = [
                    'username' => 'testuser_' . time(),
                    'email' => 'test' . time() . '@example.com',
                    'password' => 'testpass123',
                    'full_name' => 'ผู้ทดสอบ ' . date('H:i:s')
                ];
                
                $result = $userModel->create($newUserData);
                
                if ($result['success']) {
                    echo "<div class='success'>";
                    echo "<h4>✅ สร้างผู้ใช้สำเร็จ!</h4>";
                    echo "<p><strong>ID:</strong> " . $result['data']['id'] . "</p>";
                    echo "<p><strong>Username:</strong> " . $result['data']['username'] . "</p>";
                    echo "<p><strong>Email:</strong> " . $result['data']['email'] . "</p>";
                    echo "<p><strong>ชื่อเต็ม:</strong> " . $result['data']['full_name'] . "</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>";
                    echo "<h4>❌ การสร้างผู้ใช้ล้มเหลว!</h4>";
                    echo "<p>" . $result['message'] . "</p>";
                    echo "</div>";
                }

                echo "<h3>2.3 การทดสอบการเข้าสู่ระบบ</h3>";
                
                // ทดสอบ login
                $loginResult = $userModel->authenticate('admin', 'password');
                
                if ($loginResult['success']) {
                    echo "<div class='success'>";
                    echo "<h4>✅ เข้าสู่ระบบสำเร็จ!</h4>";
                    echo "<p><strong>ผู้ใช้:</strong> " . $loginResult['data']['username'] . "</p>";
                    echo "<p><strong>ชื่อเต็ม:</strong> " . $loginResult['data']['full_name'] . "</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>";
                    echo "<h4>❌ เข้าสู่ระบบล้มเหลว!</h4>";
                    echo "<p>" . $loginResult['message'] . "</p>";
                    echo "</div>";
                }
                
                // ทดสอบ login ผิด
                $wrongLoginResult = $userModel->authenticate('wronguser', 'wrongpass');
                if (!$wrongLoginResult['success']) {
                    echo "<div class='info'>";
                    echo "<h4>ℹ️ ทดสอบ login ผิด:</h4>";
                    echo "<p>" . $wrongLoginResult['message'] . "</p>";
                    echo "</div>";
                }

                echo "<h3>2.4 การค้นหาผู้ใช้</h3>";
                
                $searchResults = $userModel->searchUsers('admin');
                if (!empty($searchResults)) {
                    echo "<p><strong>ผลการค้นหา 'admin':</strong></p>";
                    echo "<table>";
                    echo "<tr><th>Username</th><th>Email</th><th>ชื่อเต็ม</th></tr>";
                    foreach ($searchResults as $user) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>3. การจัดการโพสต์ (Post Management)</h2>
            <div class="output">
                <?php
                echo "<h3>3.1 การแสดงรายการโพสต์ทั้งหมด</h3>";
                
                $postModel = new Post();
                $posts = $postModel->getPostsWithUsers();
                
                if (!empty($posts)) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>หัวข้อ</th><th>ผู้เขียน</th><th>สถานะ</th><th>วันที่สร้าง</th></tr>";
                    
                    foreach ($posts as $post) {
                        echo "<tr>";
                        echo "<td>" . $post['id'] . "</td>";
                        echo "<td>" . htmlspecialchars(substr($post['title'], 0, 50)) . "...</td>";
                        echo "<td>" . htmlspecialchars($post['full_name'] ?? $post['username']) . "</td>";
                        echo "<td><span class='status-" . $post['status'] . "'>" . ucfirst($post['status']) . "</span></td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($post['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>ไม่พบข้อมูลโพสต์</p>";
                }

                echo "<h3>3.2 การสร้างโพสต์ใหม่</h3>";
                
                // สร้างโพสต์ใหม่
                $newPostData = [
                    'user_id' => 1, // สมมติว่าใช้ user ID 1
                    'title' => 'บทความทดสอบ - ' . date('Y-m-d H:i:s'),
                    'content' => 'นี่คือเนื้อหาทดสอบสำหรับบทความใหม่ ที่สร้างขึ้นโดยระบบ Lab PHP Database',
                    'status' => 'published'
                ];
                
                $postResult = $postModel->create($newPostData);
                
                if ($postResult['success']) {
                    echo "<div class='success'>";
                    echo "<h4>✅ สร้างโพสต์สำเร็จ!</h4>";
                    echo "<p><strong>ID:</strong> " . $postResult['data']['id'] . "</p>";
                    echo "<p><strong>หัวข้อ:</strong> " . $postResult['data']['title'] . "</p>";
                    echo "<p><strong>สถานะ:</strong> " . $postResult['data']['status'] . "</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>";
                    echo "<h4>❌ การสร้างโพสต์ล้มเหลว!</h4>";
                    echo "<p>" . $postResult['message'] . "</p>";
                    echo "</div>";
                }

                echo "<h3>3.3 โพสต์ที่ได้รับการเผยแพร่</h3>";
                
                $publishedPosts = $postModel->getPublishedPosts();
                echo "<p><strong>จำนวนโพสต์ที่เผยแพร่:</strong> " . count($publishedPosts) . " โพสต์</p>";
                
                if (!empty($publishedPosts)) {
                    echo "<table>";
                    echo "<tr><th>หัวข้อ</th><th>ผู้เขียน</th><th>วันที่เผยแพร่</th></tr>";
                    
                    $displayCount = 0;
                    foreach ($publishedPosts as $post) {
                        if ($displayCount >= 5) break; // แสดงเฉพาะ 5 รายการแรก
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($post['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($post['full_name'] ?? $post['username']) . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($post['created_at'])) . "</td>";
                        echo "</tr>";
                        $displayCount++;
                    }
                    echo "</table>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>4. การจัดการหมวดหมู่ (Category Management)</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 การแสดงรายการหมวดหมู่</h3>";
                
                $categoryModel = new Category();
                $categories = $categoryModel->getCategoriesWithPostCount();
                
                if (!empty($categories)) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>ชื่อหมวดหมู่</th><th>คำอธิบาย</th><th>จำนวนโพสต์</th><th>วันที่สร้าง</th></tr>";
                    
                    foreach ($categories as $category) {
                        echo "<tr>";
                        echo "<td>" . $category['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($category['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($category['description'] ?? 'ไม่มีคำอธิบาย') . "</td>";
                        echo "<td>" . $category['post_count'] . " โพสต์</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($category['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>ไม่พบข้อมูลหมวดหมู่</p>";
                }

                echo "<h3>4.2 การสร้างหมวดหมู่ใหม่</h3>";
                
                // สร้างหมวดหมู่ใหม่
                $newCategoryData = [
                    'name' => 'Test Category ' . time(),
                    'description' => 'หมวดหมู่ทดสอบที่สร้างขึ้นเพื่อการเรียนรู้'
                ];
                
                $categoryResult = $categoryModel->create($newCategoryData);
                
                if ($categoryResult['success']) {
                    echo "<div class='success'>";
                    echo "<h4>✅ สร้างหมวดหมู่สำเร็จ!</h4>";
                    echo "<p><strong>ID:</strong> " . $categoryResult['data']['id'] . "</p>";
                    echo "<p><strong>ชื่อ:</strong> " . $categoryResult['data']['name'] . "</p>";
                    echo "<p><strong>คำอธิบาย:</strong> " . $categoryResult['data']['description'] . "</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>";
                    echo "<h4>❌ การสร้างหมวดหมู่ล้มเหลว!</h4>";
                    echo "<p>" . $categoryResult['message'] . "</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>5. สถิติฐานข้อมูล</h2>
            <div class="output">
                <?php
                echo "<h3>5.1 สรุปข้อมูลในระบบ</h3>";
                
                try {
                    $totalUsers = $userModel->count();
                    $totalPosts = $postModel->count();
                    $totalCategories = $categoryModel->count();
                    
                    echo "<table>";
                    echo "<tr><th>รายการ</th><th>จำนวน</th></tr>";
                    echo "<tr><td>ผู้ใช้ทั้งหมด</td><td>$totalUsers คน</td></tr>";
                    echo "<tr><td>โพสต์ทั้งหมด</td><td>$totalPosts โพสต์</td></tr>";
                    echo "<tr><td>หมวดหมู่ทั้งหมด</td><td>$totalCategories หมวดหมู่</td></tr>";
                    echo "</table>";
                    
                    // สถิติเพิ่มเติม
                    $publishedCount = count($postModel->getPublishedPosts());
                    
                    $stmt = $db->query("SELECT status, COUNT(*) as count FROM posts GROUP BY status");
                    $statusCounts = $stmt->fetchAll();
                    
                    echo "<h3>5.2 สถิติโพสต์ตามสถานะ</h3>";
                    echo "<table>";
                    echo "<tr><th>สถานะ</th><th>จำนวน</th></tr>";
                    
                    foreach ($statusCounts as $status) {
                        echo "<tr>";
                        echo "<td><span class='status-" . $status['status'] . "'>" . ucfirst($status['status']) . "</span></td>";
                        echo "<td>" . $status['count'] . " โพสต์</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                } catch (Exception $e) {
                    echo "<div class='error'>";
                    echo "<h4>❌ เกิดข้อผิดพลาดในการดึงสถิติ!</h4>";
                    echo "<p>" . $e->getMessage() . "</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>6. การทดสอบ Transaction</h2>
            <div class="output">
                <?php
                echo "<h3>6.1 ทดสอบการใช้งาน Transaction</h3>";
                
                try {
                    // เริ่ม transaction
                    $db->beginTransaction();
                    
                    // สร้าง user ใหม่
                    $transactionUserData = [
                        'username' => 'transaction_user_' . time(),
                        'email' => 'transaction' . time() . '@example.com',
                        'password' => 'transactionpass123',
                        'full_name' => 'ผู้ใช้ Transaction Test'
                    ];
                    
                    $userResult = $userModel->create($transactionUserData);
                    
                    if ($userResult['success']) {
                        $newUserId = $userResult['data']['id'];
                        
                        // สร้าง post สำหรับ user ใหม่
                        $transactionPostData = [
                            'user_id' => $newUserId,
                            'title' => 'โพสต์ Transaction Test',
                            'content' => 'นี่คือโพสต์ที่สร้างในรูปแบบ transaction',
                            'status' => 'published'
                        ];
                        
                        $postResult = $postModel->create($transactionPostData);
                        
                        if ($postResult['success']) {
                            // commit transaction
                            $db->commit();
                            
                            echo "<div class='success'>";
                            echo "<h4>✅ Transaction สำเร็จ!</h4>";
                            echo "<p>สร้าง User และ Post พร้อมกันด้วย Transaction</p>";
                            echo "<p><strong>User ID:</strong> " . $newUserId . "</p>";
                            echo "<p><strong>Post ID:</strong> " . $postResult['data']['id'] . "</p>";
                            echo "</div>";
                        } else {
                            throw new Exception("ไม่สามารถสร้าง Post ได้");
                        }
                    } else {
                        throw new Exception("ไม่สามารถสร้าง User ได้");
                    }
                    
                } catch (Exception $e) {
                    // rollback transaction
                    $db->rollback();
                    
                    echo "<div class='error'>";
                    echo "<h4>❌ Transaction ล้มเหลว - Rollback แล้ว!</h4>";
                    echo "<p>" . $e->getMessage() . "</p>";
                    echo "</div>";
                }

                echo "<h3>6.2 ทดสอบ Prepared Statement</h3>";
                
                // ทดสอบ prepared statement เพื่อป้องกัน SQL Injection
                $safeSearch = "admin'; DROP TABLE users; --";
                $safeResults = $userModel->searchUsers($safeSearch);
                
                echo "<div class='info'>";
                echo "<h4>ℹ️ ทดสอบป้องกัน SQL Injection</h4>";
                echo "<p><strong>คำค้นหา:</strong> " . htmlspecialchars($safeSearch) . "</p>";
                echo "<p><strong>ผลลัพธ์:</strong> " . count($safeResults) . " รายการ (ระบบปลอดภัย)</p>";
                echo "<p>Prepared Statement ป้องกัน SQL Injection ได้อย่างมีประสิทธิภาพ</p>";
                echo "</div>";
                ?>
            </div>
        </div>

        <div class="info">
            <h3>📝 สรุปความรู้ที่ได้เรียน:</h3>
            <ul>
                <li><strong>PDO Connection:</strong> การเชื่อมต่อฐานข้อมูลด้วย PDO แบบ Singleton Pattern</li>
                <li><strong>Model Classes:</strong> การสร้าง Model class สำหรับจัดการข้อมูล</li>
                <li><strong>CRUD Operations:</strong> Create, Read, Update, Delete ด้วย prepared statements</li>
                <li><strong>Error Handling:</strong> การจัดการข้อผิดพลาดอย่างมีระบบ</li>
                <li><strong>Data Validation:</strong> การตรวจสอบข้อมูลก่อนบันทึก</li>
                <li><strong>Password Hashing:</strong> การเข้ารหัสรหัสผ่านด้วย password_hash()</li>
                <li><strong>SQL Injection Prevention:</strong> การป้องกัน SQL Injection ด้วย prepared statements</li>
                <li><strong>Database Transactions:</strong> การใช้ transaction เพื่อความปลอดภัยของข้อมูล</li>
                <li><strong>Join Queries:</strong> การ join ตารางเพื่อดึงข้อมูลที่เกี่ยวข้อง</li>
                <li><strong>Database Statistics:</strong> การสร้างรายงานสถิติจากฐานข้อมูล</li>
            </ul>
            
            <h3>🔗 ลิงก์ที่เกี่ยวข้อง:</h3>
            <p>
                <a href="../" class="btn">🏠 กลับหน้าหลัก</a>
                <a href="02-crud-operations.php" class="btn">➡️ CRUD Operations</a>
                <a href="03-advanced-queries.php" class="btn">➡️ Advanced Queries</a>
            </p>
        </div>
    </div>
</body>
</html>