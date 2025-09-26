<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 03: Redis Cache - การใช้งาน Redis</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #e74c3c; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background-color: #d5f4e6; border-left: 4px solid #27ae60; padding: 15px; margin: 10px 0; }
        .error { background-color: #fadbd8; border-left: 4px solid #e74c3c; padding: 15px; margin: 10px 0; }
        .info { background-color: #d5dbdb; border-left: 4px solid #3498db; padding: 15px; margin: 10px 0; }
        .warning { background-color: #fcf3cf; border-left: 4px solid #f1c40f; padding: 15px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #bdc3c7; padding: 8px; text-align: left; }
        th { background-color: #e74c3c; color: white; }
        .btn { display: inline-block; padding: 8px 15px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #c0392b; }
        .metric { background: #3498db; color: white; padding: 10px; border-radius: 5px; margin: 5px; display: inline-block; min-width: 120px; text-align: center; }
        .counter { background: #27ae60; color: white; padding: 10px; border-radius: 5px; margin: 5px; display: inline-block; min-width: 120px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 03: Redis Cache - การใช้งาน Redis</h1>

        <?php
        require_once 'redis_manager.php';

        // ตรวจสอบการเชื่อมต่อ Redis
        echo "<div class='lab-section'>";
        echo "<h2>1. การทดสอบการเชื่อมต่อ Redis</h2>";
        
        try {
            $redisManager = RedisManager::getInstance();
            
            if ($redisManager->isConnected()) {
                $connectionInfo = $redisManager->getConnectionInfo();
                
                echo "<div class='success'>";
                echo "<h3>✅ เชื่อมต่อ Redis สำเร็จ!</h3>";
                echo "<p><strong>Host:</strong> " . RedisConfig::HOST . ":" . RedisConfig::PORT . "</p>";
                echo "<p><strong>Database:</strong> " . RedisConfig::DATABASE . "</p>";
                echo "</div>";
                
                echo "<div class='info'>";
                echo "<h4>📊 ข้อมูล Redis Server:</h4>";
                echo "<div class='metric'>Redis Version<br><strong>" . $connectionInfo['redis_version'] . "</strong></div>";
                echo "<div class='metric'>Connected Clients<br><strong>" . $connectionInfo['connected_clients'] . "</strong></div>";
                echo "<div class='metric'>Memory Usage<br><strong>" . $connectionInfo['used_memory_human'] . "</strong></div>";
                echo "<div class='metric'>Total Connections<br><strong>" . number_format($connectionInfo['total_connections_received']) . "</strong></div>";
                echo "<div class='metric'>Commands Processed<br><strong>" . number_format($connectionInfo['total_commands_processed']) . "</strong></div>";
                echo "<div class='metric'>Cache Hit Rate<br><strong>" . 
                     ($connectionInfo['keyspace_hits'] > 0 ? 
                      round(($connectionInfo['keyspace_hits'] / ($connectionInfo['keyspace_hits'] + $connectionInfo['keyspace_misses'])) * 100, 2) . '%' : 
                      '0%') . "</strong></div>";
                echo "</div>";
                
            } else {
                throw new Exception("ไม่สามารถเชื่อมต่อ Redis ได้");
            }
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<h3>❌ การเชื่อมต่อ Redis ล้มเหลว!</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "</div>";
            exit;
        }
        echo "</div>";
        ?>

        <div class="lab-section">
            <h2>2. การใช้งาน Cache Manager</h2>
            <div class="output">
                <?php
                echo "<h3>2.1 การบันทึกและดึงข้อมูล Cache</h3>";
                
                $cacheManager = new CacheManager();
                
                // ทดสอบการบันทึกข้อมูล
                $testData = [
                    'user_id' => 123,
                    'username' => 'testuser',
                    'email' => 'test@example.com',
                    'preferences' => [
                        'theme' => 'dark',
                        'language' => 'th',
                        'notifications' => true
                    ]
                ];
                
                $cacheKey = 'user_profile_123';
                $cacheTtl = 300; // 5 minutes
                
                if ($cacheManager->set($cacheKey, $testData, $cacheTtl)) {
                    echo "<div class='success'>";
                    echo "<h4>✅ บันทึก Cache สำเร็จ!</h4>";
                    echo "<p><strong>Key:</strong> $cacheKey</p>";
                    echo "<p><strong>TTL:</strong> $cacheTtl วินาที</p>";
                    echo "<p><strong>ข้อมูล:</strong> " . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>❌ ไม่สามารถบันทึก Cache ได้</div>";
                }
                
                // ทดสอบการดึงข้อมูล
                $cachedData = $cacheManager->get($cacheKey);
                if ($cachedData !== null) {
                    echo "<div class='success'>";
                    echo "<h4>✅ ดึงข้อมูล Cache สำเร็จ!</h4>";
                    echo "<p><strong>ข้อมูลที่ดึงได้:</strong></p>";
                    echo "<pre>" . json_encode($cachedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                    echo "<p><strong>TTL เหลืออีก:</strong> " . $cacheManager->getTtl($cacheKey) . " วินาที</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>❌ ไม่พบข้อมูล Cache</div>";
                }

                echo "<h3>2.2 การทดสอบ Cache-aside Pattern</h3>";
                
                // สร้างฟังก์ชันจำลองการดึงข้อมูลจากฐานข้อมูล
                $expensiveOperation = function() {
                    // จำลองการคำนวณที่ใช้เวลานาน
                    usleep(500000); // 0.5 seconds
                    return [
                        'data' => 'ข้อมูลจากฐานข้อมูล',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'calculation_result' => rand(1000, 9999)
                    ];
                };
                
                $cacheKey2 = 'expensive_operation_result';
                
                // ครั้งแรก - จะต้องคำนวณใหม่
                $startTime = microtime(true);
                $result1 = $cacheManager->remember($cacheKey2, $expensiveOperation, 600);
                $time1 = round((microtime(true) - $startTime) * 1000, 2);
                
                echo "<div class='info'>";
                echo "<h4>🔄 การเรียกครั้งแรก (ไม่มี Cache):</h4>";
                echo "<p><strong>เวลาที่ใช้:</strong> {$time1} มิลลิวินาที</p>";
                echo "<p><strong>ผลลัพธ์:</strong> " . json_encode($result1, JSON_UNESCAPED_UNICODE) . "</p>";
                echo "</div>";
                
                // ครั้งที่สอง - จะดึงจาก Cache
                $startTime = microtime(true);
                $result2 = $cacheManager->remember($cacheKey2, $expensiveOperation, 600);
                $time2 = round((microtime(true) - $startTime) * 1000, 2);
                
                echo "<div class='success'>";
                echo "<h4>⚡ การเรียกครั้งที่สอง (มี Cache):</h4>";
                echo "<p><strong>เวลาที่ใช้:</strong> {$time2} มิลลิวินาที</p>";
                echo "<p><strong>ผลลัพธ์:</strong> " . json_encode($result2, JSON_UNESCAPED_UNICODE) . "</p>";
                echo "<p><strong>ประสิทธิภาพดีขึ้น:</strong> " . round(($time1 - $time2) / $time1 * 100, 1) . "%</p>";
                echo "</div>";

                echo "<h3>2.3 สถิติ Cache ปัจจุบัน</h3>";
                
                $cacheStats = $cacheManager->getStats();
                
                echo "<div class='info'>";
                echo "<h4>📈 Cache Statistics:</h4>";
                echo "<p><strong>จำนวน Keys ทั้งหมด:</strong> " . $cacheStats['total_keys'] . "</p>";
                
                if (!empty($cacheStats['keys'])) {
                    echo "<table>";
                    echo "<tr><th>Cache Key</th><th>TTL (วินาที)</th><th>Type</th><th>Size (bytes)</th></tr>";
                    
                    foreach ($cacheStats['keys'] as $keyInfo) {
                        $ttlDisplay = $keyInfo['ttl'] > 0 ? $keyInfo['ttl'] : ($keyInfo['ttl'] == -1 ? 'ไม่หมดอายุ' : 'หมดอายุแล้ว');
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($keyInfo['key']) . "</td>";
                        echo "<td>$ttlDisplay</td>";
                        echo "<td>" . $keyInfo['type'] . "</td>";
                        echo "<td>" . number_format($keyInfo['size']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                echo "</div>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>3. การใช้งาน Session Management</h2>
            <div class="output">
                <?php
                echo "<h3>3.1 การสร้างและจัดการ Session</h3>";
                
                $sessionManager = new RedisSessionManager();
                
                // สร้าง session ใหม่
                $sessionId = $sessionManager->start();
                
                echo "<div class='success'>";
                echo "<h4>✅ สร้าง Session สำเร็จ!</h4>";
                echo "<p><strong>Session ID:</strong> $sessionId</p>";
                echo "</div>";
                
                // บันทึกข้อมูลใน session
                $sessionManager->set($sessionId, 'user_id', 123);
                $sessionManager->set($sessionId, 'username', 'testuser');
                $sessionManager->set($sessionId, 'login_time', date('Y-m-d H:i:s'));
                $sessionManager->set($sessionId, 'preferences', [
                    'theme' => 'dark',
                    'language' => 'th'
                ]);
                
                echo "<div class='info'>";
                echo "<h4>📝 บันทึกข้อมูลใน Session:</h4>";
                echo "<ul>";
                echo "<li>user_id: 123</li>";
                echo "<li>username: testuser</li>";
                echo "<li>login_time: " . date('Y-m-d H:i:s') . "</li>";
                echo "<li>preferences: theme=dark, language=th</li>";
                echo "</ul>";
                echo "</div>";
                
                // ดึงข้อมูลจาก session
                $allSessionData = $sessionManager->get($sessionId);
                $specificValue = $sessionManager->get($sessionId, 'username');
                
                echo "<div class='success'>";
                echo "<h4>✅ ดึงข้อมูล Session:</h4>";
                echo "<p><strong>ข้อมูลทั้งหมด:</strong></p>";
                echo "<pre>" . json_encode($allSessionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                echo "<p><strong>Username:</strong> $specificValue</p>";
                echo "</div>";
                
                // สร้าง session เพิ่มเติมเพื่อทดสอบ
                $sessionId2 = $sessionManager->start();
                $sessionManager->set($sessionId2, 'user_id', 456);
                $sessionManager->set($sessionId2, 'username', 'anotheruser');
                $sessionManager->set($sessionId2, 'role', 'admin');

                echo "<h3>3.2 รายการ Session ทั้งหมด</h3>";
                
                $allSessions = $sessionManager->getAllSessions();
                
                if (!empty($allSessions)) {
                    echo "<table>";
                    echo "<tr><th>Session ID</th><th>TTL (วินาที)</th><th>User ID</th><th>Username</th><th>ข้อมูลเพิ่มเติม</th></tr>";
                    
                    foreach ($allSessions as $session) {
                        $ttlDisplay = $session['ttl'] > 0 ? $session['ttl'] : 'หมดอายุ';
                        $userId = $session['data']['user_id'] ?? 'N/A';
                        $username = $session['data']['username'] ?? 'N/A';
                        
                        $additionalData = array_diff_key($session['data'], ['user_id' => '', 'username' => '']);
                        $additionalInfo = !empty($additionalData) ? json_encode($additionalData, JSON_UNESCAPED_UNICODE) : 'ไม่มี';
                        
                        echo "<tr>";
                        echo "<td>" . substr($session['session_id'], 0, 16) . "...</td>";
                        echo "<td>$ttlDisplay</td>";
                        echo "<td>$userId</td>";
                        echo "<td>$username</td>";
                        echo "<td>" . htmlspecialchars($additionalInfo) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    echo "<p><strong>จำนวน Session ทั้งหมด:</strong> " . count($allSessions) . "</p>";
                }

                echo "<h3>3.3 การยืดอายุ Session</h3>";
                
                if ($sessionManager->extendSession($sessionId, 3600)) { // ยืดอายุ 1 ชั่วโมง
                    echo "<div class='success'>";
                    echo "<h4>✅ ยืดอายุ Session สำเร็จ!</h4>";
                    echo "<p>Session จะหมดอายุในอีก 3600 วินาที (1 ชั่วโมง)</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>4. Counter และ Rate Limiting</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 Counter System</h3>";
                
                $counter = new RedisCounter();
                
                // สร้างและทดสอบ counter
                $counter->increment('page_views', 5);
                $counter->increment('user_logins', 2);
                $counter->increment('api_calls', 10);
                $counter->increment('downloads', 3);
                
                // แสดงผล counter
                $allCounters = $counter->getAllCounters();
                
                echo "<div class='info'>";
                echo "<h4>🔢 System Counters:</h4>";
                foreach ($allCounters as $name => $value) {
                    echo "<div class='counter'>";
                    echo "<strong>" . ucwords(str_replace('_', ' ', $name)) . "</strong><br>";
                    echo "<big>" . number_format($value) . "</big>";
                    echo "</div>";
                }
                echo "</div>";
                
                // ทดสอบการเพิ่มและลด
                $counter->increment('test_counter', 10);
                $before = $counter->get('test_counter');
                $counter->decrement('test_counter', 3);
                $after = $counter->get('test_counter');
                
                echo "<div class='success'>";
                echo "<h4>✅ ทดสอบ Counter Operations:</h4>";
                echo "<ul>";
                echo "<li>เริ่มต้น: 0</li>";
                echo "<li>เพิ่ม 10: $before</li>";
                echo "<li>ลด 3: $after</li>";
                echo "</ul>";
                echo "</div>";

                echo "<h3>4.2 Rate Limiting</h3>";
                
                $rateLimiter = new RateLimiter();
                $clientId = 'client_' . $_SERVER['REMOTE_ADDR'];
                $maxRequests = 5;
                $windowSeconds = 60; // 1 minute
                
                echo "<div class='info'>";
                echo "<h4>⏱️ Rate Limiting Configuration:</h4>";
                echo "<p><strong>Client ID:</strong> $clientId</p>";
                echo "<p><strong>Max Requests:</strong> $maxRequests per $windowSeconds seconds</p>";
                echo "</div>";
                
                // ทดสอบ rate limiting
                $testResults = [];
                for ($i = 1; $i <= 7; $i++) {
                    $isAllowed = $rateLimiter->isAllowed($clientId, $maxRequests, $windowSeconds);
                    $remaining = $rateLimiter->getRemainingRequests($clientId, $maxRequests, $windowSeconds);
                    $resetTime = $rateLimiter->getTimeToReset($clientId, $windowSeconds);
                    
                    $testResults[] = [
                        'request' => $i,
                        'allowed' => $isAllowed,
                        'remaining' => $remaining,
                        'reset_in' => $resetTime
                    ];
                }
                
                echo "<div class='output'>";
                echo "<h4>🧪 Rate Limiting Test Results:</h4>";
                echo "<table>";
                echo "<tr><th>Request #</th><th>Allowed</th><th>Remaining</th><th>Reset In (sec)</th></tr>";
                
                foreach ($testResults as $result) {
                    $allowedText = $result['allowed'] ? '✅ Yes' : '❌ No';
                    $rowClass = $result['allowed'] ? '' : 'style="background-color: #fadbd8;"';
                    
                    echo "<tr $rowClass>";
                    echo "<td>" . $result['request'] . "</td>";
                    echo "<td>$allowedText</td>";
                    echo "<td>" . $result['remaining'] . "</td>";
                    echo "<td>" . $result['reset_in'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
                
                if (in_array(false, array_column($testResults, 'allowed'))) {
                    echo "<div class='warning'>";
                    echo "<h4>⚠️ Rate Limit Reached!</h4>";
                    echo "<p>บาง request ถูกปฏิเสธเนื่องจากเกิน rate limit ที่กำหนด</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>5. Performance Testing</h2>
            <div class="output">
                <?php
                echo "<h3>5.1 การทดสอบประสิทธิภาพ Cache</h3>";
                
                // ทดสอบประสิทธิภาพการเขียน
                $writeStartTime = microtime(true);
                $writeOperations = 100;
                
                for ($i = 0; $i < $writeOperations; $i++) {
                    $cacheManager->set("perf_test_$i", [
                        'id' => $i,
                        'data' => 'Test data ' . $i,
                        'timestamp' => time()
                    ], 300);
                }
                
                $writeTime = (microtime(true) - $writeStartTime) * 1000;
                
                // ทดสอบประสิทธิภาพการอ่าน
                $readStartTime = microtime(true);
                $readOperations = 100;
                
                for ($i = 0; $i < $readOperations; $i++) {
                    $cacheManager->get("perf_test_$i");
                }
                
                $readTime = (microtime(true) - $readStartTime) * 1000;
                
                echo "<div class='info'>";
                echo "<h4>⚡ Performance Test Results:</h4>";
                echo "<table>";
                echo "<tr><th>Operation</th><th>Count</th><th>Total Time (ms)</th><th>Average Time (ms)</th><th>Operations/sec</th></tr>";
                echo "<tr>";
                echo "<td>Write (SET)</td>";
                echo "<td>$writeOperations</td>";
                echo "<td>" . number_format($writeTime, 2) . "</td>";
                echo "<td>" . number_format($writeTime / $writeOperations, 3) . "</td>";
                echo "<td>" . number_format($writeOperations / ($writeTime / 1000), 0) . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td>Read (GET)</td>";
                echo "<td>$readOperations</td>";
                echo "<td>" . number_format($readTime, 2) . "</td>";
                echo "<td>" . number_format($readTime / $readOperations, 3) . "</td>";
                echo "<td>" . number_format($readOperations / ($readTime / 1000), 0) . "</td>";
                echo "</tr>";
                echo "</table>";
                echo "</div>";
                
                // ทำความสะอาด performance test data
                for ($i = 0; $i < $writeOperations; $i++) {
                    $cacheManager->delete("perf_test_$i");
                }

                echo "<h3>5.2 Memory Usage Analysis</h3>";
                
                $connectionInfo = $redisManager->getConnectionInfo();
                
                echo "<div class='info'>";
                echo "<h4>💾 Memory Usage:</h4>";
                echo "<p><strong>Used Memory:</strong> " . $connectionInfo['used_memory_human'] . "</p>";
                echo "<p><strong>Total Commands:</strong> " . number_format($connectionInfo['total_commands_processed']) . "</p>";
                echo "<p><strong>Cache Efficiency:</strong> " . 
                     ($connectionInfo['keyspace_hits'] > 0 ? 
                      round(($connectionInfo['keyspace_hits'] / ($connectionInfo['keyspace_hits'] + $connectionInfo['keyspace_misses'])) * 100, 2) . '%' : 
                      'N/A') . "</p>";
                echo "</div>";
                ?>
            </div>
        </div>

        <div class="info">
            <h3>📝 สรุปความรู้ที่ได้เรียน:</h3>
            <ul>
                <li><strong>Redis Connection:</strong> การเชื่อมต่อ Redis ด้วย Singleton Pattern</li>
                <li><strong>Cache Management:</strong> การจัดการ Cache ด้วย set, get, delete และ TTL</li>
                <li><strong>Cache-aside Pattern:</strong> รูปแบบการใช้ Cache เพื่อเพิ่มประสิทธิภาพ</li>
                <li><strong>Session Storage:</strong> การใช้ Redis เป็น Session Store</li>
                <li><strong>Counter System:</strong> การใช้ Redis สำหรับ Counter และสถิติ</li>
                <li><strong>Rate Limiting:</strong> การจำกัดอัตราการเรียกใช้ API</li>
                <li><strong>Performance Testing:</strong> การทดสอบประสิทธิภาพของ Cache</li>
                <li><strong>Data Serialization:</strong> การจัดเก็บข้อมูลที่ซับซ้อนใน Redis</li>
                <li><strong>TTL Management:</strong> การจัดการอายุของข้อมูลใน Cache</li>
                <li><strong>Redis Statistics:</strong> การติดตามและวิเคราะห์การใช้งาน Redis</li>
            </ul>
            
            <h3>🔗 ลิงก์ที่เกี่ยวข้อง:</h3>
            <p>
                <a href="../" class="btn">🏠 กลับหน้าหลัก</a>
                <a href="02-advanced-caching.php" class="btn">➡️ Advanced Caching</a>
                <a href="../lab04-memcache/" class="btn">➡️ Lab 04: Memcache</a>
            </p>
        </div>
    </div>
</body>
</html>