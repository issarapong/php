<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 04: Memcache - การใช้งาน Memcache</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #9b59b6; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background-color: #d5f4e6; border-left: 4px solid #27ae60; padding: 15px; margin: 10px 0; }
        .error { background-color: #fadbd8; border-left: 4px solid #e74c3c; padding: 15px; margin: 10px 0; }
        .info { background-color: #d5dbdb; border-left: 4px solid #3498db; padding: 15px; margin: 10px 0; }
        .warning { background-color: #fcf3cf; border-left: 4px solid #f1c40f; padding: 15px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #bdc3c7; padding: 8px; text-align: left; }
        th { background-color: #9b59b6; color: white; }
        .btn { display: inline-block; padding: 8px 15px; background: #9b59b6; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #8e44ad; }
        .metric { background: #3498db; color: white; padding: 10px; border-radius: 5px; margin: 5px; display: inline-block; min-width: 120px; text-align: center; }
        .stat { background: #27ae60; color: white; padding: 10px; border-radius: 5px; margin: 5px; display: inline-block; min-width: 120px; text-align: center; }
        .perf { background: #e67e22; color: white; padding: 10px; border-radius: 5px; margin: 5px; display: inline-block; min-width: 120px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 04: Memcache - การใช้งาน Memcache</h1>

        <?php
        require_once 'memcache_manager.php';

        // ตรวจสอบการเชื่อมต่อ Memcache
        echo "<div class='lab-section'>";
        echo "<h2>1. การทดสอบการเชื่อมต่อ Memcache</h2>";
        
        try {
            $memcacheManager = MemcacheManager::getInstance();
            
            if ($memcacheManager->isConnected()) {
                $version = $memcacheManager->getVersion();
                $stats = $memcacheManager->getStats();
                
                echo "<div class='success'>";
                echo "<h3>✅ เชื่อมต่อ Memcache สำเร็จ!</h3>";
                echo "<p><strong>Host:</strong> " . MemcacheConfig::HOST . ":" . MemcacheConfig::PORT . "</p>";
                echo "<p><strong>Version:</strong> " . ($version ?: 'N/A') . "</p>";
                echo "</div>";
                
                if ($stats) {
                    echo "<div class='info'>";
                    echo "<h4>📊 ข้อมูล Memcache Server:</h4>";
                    echo "<div class='metric'>Version<br><strong>" . ($stats['version'] ?? 'N/A') . "</strong></div>";
                    echo "<div class='metric'>Uptime<br><strong>" . gmdate("H:i:s", $stats['uptime'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Current Items<br><strong>" . number_format($stats['curr_items'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Total Items<br><strong>" . number_format($stats['total_items'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Current Connections<br><strong>" . ($stats['curr_connections'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Total Connections<br><strong>" . number_format($stats['total_connections'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Get Commands<br><strong>" . number_format($stats['cmd_get'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Set Commands<br><strong>" . number_format($stats['cmd_set'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Get Hits<br><strong>" . number_format($stats['get_hits'] ?? 0) . "</strong></div>";
                    echo "<div class='metric'>Get Misses<br><strong>" . number_format($stats['get_misses'] ?? 0) . "</strong></div>";
                    
                    // คำนวณ hit rate
                    $hitRate = 0;
                    if (isset($stats['get_hits']) && isset($stats['get_misses'])) {
                        $totalGets = $stats['get_hits'] + $stats['get_misses'];
                        if ($totalGets > 0) {
                            $hitRate = ($stats['get_hits'] / $totalGets) * 100;
                        }
                    }
                    echo "<div class='metric'>Hit Rate<br><strong>" . number_format($hitRate, 1) . "%</strong></div>";
                    
                    echo "<div class='metric'>Memory Used<br><strong>" . 
                         number_format(($stats['bytes'] ?? 0) / 1024 / 1024, 2) . " MB</strong></div>";
                    echo "</div>";
                }
                
            } else {
                throw new Exception("ไม่สามารถเชื่อมต่อ Memcache ได้");
            }
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<h3>❌ การเชื่อมต่อ Memcache ล้มเหลว!</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<div class='warning'>";
            echo "<p><strong>หมายเหตุ:</strong> กรุณาตรวจสอบว่า Memcache server ทำงานอยู่และ PHP extension ถูกติดตั้งแล้ว</p>";
            echo "</div>";
            echo "</div>";
            exit;
        }
        echo "</div>";
        ?>

        <div class="lab-section">
            <h2>2. การใช้งาน Memcache Cache Manager</h2>
            <div class="output">
                <?php
                echo "<h3>2.1 การบันทึกและดึงข้อมูล Cache</h3>";
                
                $cacheManager = new MemcacheCacheManager();
                
                echo "<div class='info'>";
                echo "<p><strong>Extension Type:</strong> " . $cacheManager->getExtensionType() . "</p>";
                echo "</div>";
                
                // ทดสอบการบันทึกข้อมูลต่างๆ
                $testData = [
                    'simple_string' => 'Hello Memcache!',
                    'number' => 12345,
                    'array' => ['name' => 'John', 'age' => 30, 'city' => 'Bangkok'],
                    'object' => (object)['product' => 'Laptop', 'price' => 25000, 'stock' => 10],
                    'large_data' => str_repeat('Lorem ipsum dolor sit amet. ', 100)
                ];
                
                $results = [];
                foreach ($testData as $key => $value) {
                    $success = $cacheManager->set($key, $value, 300); // 5 minutes TTL
                    $results[$key] = $success;
                }
                
                echo "<div class='success'>";
                echo "<h4>✅ การบันทึก Cache:</h4>";
                echo "<table>";
                echo "<tr><th>Key</th><th>Type</th><th>Status</th><th>Size (bytes)</th></tr>";
                
                foreach ($testData as $key => $value) {
                    $type = gettype($value);
                    $size = strlen(serialize($value));
                    $status = $results[$key] ? '✅ Success' : '❌ Failed';
                    
                    echo "<tr>";
                    echo "<td>$key</td>";
                    echo "<td>$type</td>";
                    echo "<td>$status</td>";
                    echo "<td>" . number_format($size) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
                
                // ทดสอบการดึงข้อมูล
                echo "<h4>การดึงข้อมูลจาก Cache:</h4>";
                
                foreach ($testData as $key => $originalValue) {
                    $cachedValue = $cacheManager->get($key);
                    $match = ($cachedValue === $originalValue);
                    
                    if ($match) {
                        echo "<div class='success'>";
                        echo "<p><strong>$key:</strong> ✅ ตรงกับข้อมูลต้นฉบับ</p>";
                        echo "</div>";
                    } else {
                        echo "<div class='error'>";
                        echo "<p><strong>$key:</strong> ❌ ไม่ตรงกับข้อมูลต้นฉบับ</p>";
                        echo "</div>";
                    }
                }

                echo "<h3>2.2 การทดสอบ Multiple Operations</h3>";
                
                // ทดสอบ setMultiple
                $multipleData = [
                    'user_1' => ['name' => 'Alice', 'score' => 95],
                    'user_2' => ['name' => 'Bob', 'score' => 88],
                    'user_3' => ['name' => 'Charlie', 'score' => 92]
                ];
                
                $setMultiResult = $cacheManager->setMultiple($multipleData, 300);
                
                if ($setMultiResult) {
                    echo "<div class='success'>";
                    echo "<h4>✅ Set Multiple สำเร็จ!</h4>";
                    echo "<p>บันทึกข้อมูล " . count($multipleData) . " รายการพร้อมกัน</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>";
                    echo "<h4>❌ Set Multiple ล้มเหลว!</h4>";
                    echo "</div>";
                }
                
                // ทดสอบ getMultiple
                $keys = array_keys($multipleData);
                $retrievedData = $cacheManager->getMultiple($keys);
                
                echo "<div class='info'>";
                echo "<h4>🔍 Get Multiple Results:</h4>";
                echo "<table>";
                echo "<tr><th>Key</th><th>Found</th><th>Data</th></tr>";
                
                foreach ($keys as $key) {
                    $found = isset($retrievedData[$key]);
                    $data = $found ? json_encode($retrievedData[$key], JSON_UNESCAPED_UNICODE) : 'Not found';
                    $status = $found ? '✅ Yes' : '❌ No';
                    
                    echo "<tr>";
                    echo "<td>$key</td>";
                    echo "<td>$status</td>";
                    echo "<td>$data</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";

                echo "<h3>2.3 การทดสอบ Increment/Decrement</h3>";
                
                $counterKey = 'test_counter';
                
                // รีเซ็ต counter
                $cacheManager->delete($counterKey);
                
                // ทดสอบ increment
                $results = [];
                for ($i = 1; $i <= 5; $i++) {
                    $value = $cacheManager->increment($counterKey, $i * 2);
                    $results[] = $value;
                }
                
                echo "<div class='info'>";
                echo "<h4>📈 Increment Test Results:</h4>";
                echo "<p><strong>Increment sequence (+2, +4, +6, +8, +10):</strong> " . implode(', ', $results) . "</p>";
                echo "</div>";
                
                // ทดสอบ decrement
                $decrementResults = [];
                for ($i = 1; $i <= 3; $i++) {
                    $value = $cacheManager->decrement($counterKey, $i * 3);
                    $decrementResults[] = $value;
                }
                
                echo "<div class='info'>";
                echo "<h4>📉 Decrement Test Results:</h4>";
                echo "<p><strong>Decrement sequence (-3, -6, -9):</strong> " . implode(', ', $decrementResults) . "</p>";
                echo "</div>";

                echo "<h3>2.4 การทดสอบ Cache-aside Pattern</h3>";
                
                $expensiveFunction = function() {
                    // จำลองการคำนวณที่ใช้เวลานาน
                    usleep(300000); // 0.3 seconds
                    return [
                        'calculation_result' => rand(1000, 9999),
                        'generated_at' => date('Y-m-d H:i:s'),
                        'server_load' => round(rand(10, 90) / 10, 1)
                    ];
                };
                
                $cacheKey = 'expensive_calculation';
                
                // ครั้งแรก - ไม่มี cache
                $startTime = microtime(true);
                $result1 = $cacheManager->remember($cacheKey, $expensiveFunction, 300);
                $time1 = round((microtime(true) - $startTime) * 1000, 2);
                
                echo "<div class='info'>";
                echo "<h4>🔄 First Call (No Cache):</h4>";
                echo "<p><strong>Execution Time:</strong> {$time1} ms</p>";
                echo "<p><strong>Result:</strong> " . json_encode($result1, JSON_UNESCAPED_UNICODE) . "</p>";
                echo "</div>";
                
                // ครั้งที่สอง - มี cache
                $startTime = microtime(true);
                $result2 = $cacheManager->remember($cacheKey, $expensiveFunction, 300);
                $time2 = round((microtime(true) - $startTime) * 1000, 2);
                
                echo "<div class='success'>";
                echo "<h4>⚡ Second Call (With Cache):</h4>";
                echo "<p><strong>Execution Time:</strong> {$time2} ms</p>";
                echo "<p><strong>Result:</strong> " . json_encode($result2, JSON_UNESCAPED_UNICODE) . "</p>";
                echo "<p><strong>Performance Improvement:</strong> " . 
                     round(($time1 - $time2) / $time1 * 100, 1) . "% faster</p>";
                echo "</div>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>3. Performance Comparison</h2>
            <div class="output">
                <?php
                echo "<h3>3.1 การทดสอบประสิทธิภาพ</h3>";
                
                // ทดสอบประสิทธิภาพการเขียน
                $testIterations = 50;
                $testData = ['test_key_' => 'Test data value ' . str_repeat('x', 100)];
                
                // Write Performance Test
                $writeStartTime = microtime(true);
                for ($i = 0; $i < $testIterations; $i++) {
                    $cacheManager->set("perf_write_$i", $testData, 300);
                }
                $writeTime = (microtime(true) - $writeStartTime) * 1000;
                
                // Read Performance Test
                $readStartTime = microtime(true);
                for ($i = 0; $i < $testIterations; $i++) {
                    $cacheManager->get("perf_write_$i");
                }
                $readTime = (microtime(true) - $readStartTime) * 1000;
                
                // Delete Performance Test
                $deleteStartTime = microtime(true);
                for ($i = 0; $i < $testIterations; $i++) {
                    $cacheManager->delete("perf_write_$i");
                }
                $deleteTime = (microtime(true) - $deleteStartTime) * 1000;
                
                echo "<div class='info'>";
                echo "<h4>⚡ Performance Test Results ($testIterations operations):</h4>";
                echo "<table>";
                echo "<tr><th>Operation</th><th>Total Time (ms)</th><th>Avg Time (ms)</th><th>Operations/sec</th></tr>";
                
                $operations = [
                    'Write (SET)' => $writeTime,
                    'Read (GET)' => $readTime,
                    'Delete (DEL)' => $deleteTime
                ];
                
                foreach ($operations as $operation => $time) {
                    $avgTime = $time / $testIterations;
                    $opsPerSec = $testIterations / ($time / 1000);
                    
                    echo "<tr>";
                    echo "<td>$operation</td>";
                    echo "<td>" . number_format($time, 2) . "</td>";
                    echo "<td>" . number_format($avgTime, 3) . "</td>";
                    echo "<td>" . number_format($opsPerSec, 0) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";

                echo "<h3>3.2 การทดสอบ Data Size Impact</h3>";
                
                $dataSizes = [
                    '1KB' => str_repeat('x', 1024),
                    '10KB' => str_repeat('x', 10240),
                    '100KB' => str_repeat('x', 102400),
                    '1MB' => str_repeat('x', 1048576)
                ];
                
                echo "<div class='info'>";
                echo "<h4>📊 Data Size Performance Impact:</h4>";
                echo "<table>";
                echo "<tr><th>Size</th><th>Set Time (ms)</th><th>Get Time (ms)</th><th>Compressed</th></tr>";
                
                foreach ($dataSizes as $sizeLabel => $data) {
                    // Test SET
                    $setStart = microtime(true);
                    $setSuccess = $cacheManager->set("size_test_$sizeLabel", $data, 300);
                    $setTime = (microtime(true) - $setStart) * 1000;
                    
                    // Test GET
                    $getStart = microtime(true);
                    $retrievedData = $cacheManager->get("size_test_$sizeLabel");
                    $getTime = (microtime(true) - $getStart) * 1000;
                    
                    $compressed = (strlen($data) != strlen(serialize($retrievedData))) ? 'Yes' : 'No';
                    $status = ($setSuccess && $retrievedData === $data) ? '✅' : '❌';
                    
                    echo "<tr>";
                    echo "<td>$sizeLabel $status</td>";
                    echo "<td>" . number_format($setTime, 3) . "</td>";
                    echo "<td>" . number_format($getTime, 3) . "</td>";
                    echo "<td>$compressed</td>";
                    echo "</tr>";
                    
                    // Clean up
                    $cacheManager->delete("size_test_$sizeLabel");
                }
                echo "</table>";
                echo "</div>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>4. Distributed Caching (Advanced)</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 การทดสอบ Distributed Cache</h3>";
                
                try {
                    // สำหรับ demo ใช้ server เดียวกัน แต่ในความเป็นจริงจะเป็นหลาย server
                    $servers = [
                        ['host' => MemcacheConfig::HOST, 'port' => MemcacheConfig::PORT, 'weight' => 100]
                    ];
                    
                    $distributedCache = new DistributedMemcacheManager($servers);
                    
                    echo "<div class='success'>";
                    echo "<h4>✅ Distributed Cache Manager เริ่มต้นสำเร็จ!</h4>";
                    echo "<p><strong>Servers:</strong> " . count($servers) . " server(s)</p>";
                    echo "</div>";
                    
                    // ทดสอบการกระจายข้อมูล
                    $testKeys = ['user:1001', 'user:1002', 'user:1003', 'product:5001', 'product:5002'];
                    $testData = [
                        'user:1001' => ['name' => 'Alice', 'level' => 15],
                        'user:1002' => ['name' => 'Bob', 'level' => 23],
                        'user:1003' => ['name' => 'Charlie', 'level' => 8],
                        'product:5001' => ['name' => 'Laptop', 'price' => 25000],
                        'product:5002' => ['name' => 'Mouse', 'price' => 500]
                    ];
                    
                    echo "<div class='info'>";
                    echo "<h4>🔄 Key Distribution Test:</h4>";
                    echo "<table>";
                    echo "<tr><th>Key</th><th>Target Server</th><th>Set Status</th><th>Get Status</th></tr>";
                    
                    foreach ($testData as $key => $data) {
                        $server = $distributedCache->getServerByKey($key);
                        $serverInfo = $server ? $server['host'] . ':' . $server['port'] : 'Unknown';
                        
                        $setSuccess = $distributedCache->set($key, $data, 300);
                        $retrievedData = $distributedCache->get($key);
                        $getSuccess = ($retrievedData === $data);
                        
                        $setStatus = $setSuccess ? '✅' : '❌';
                        $getStatus = $getSuccess ? '✅' : '❌';
                        
                        echo "<tr>";
                        echo "<td>$key</td>";
                        echo "<td>$serverInfo</td>";
                        echo "<td>$setStatus</td>";
                        echo "<td>$getStatus</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                    
                    // แสดงสถิติของแต่ละ server
                    $serverStats = $distributedCache->getServerStats();
                    
                    if (!empty($serverStats)) {
                        echo "<div class='info'>";
                        echo "<h4>📈 Server Statistics:</h4>";
                        echo "<table>";
                        echo "<tr><th>Server</th><th>Items</th><th>Hits</th><th>Misses</th><th>Memory</th></tr>";
                        
                        foreach ($serverStats as $server => $stats) {
                            $memUsed = number_format(($stats['bytes'] ?? 0) / 1024 / 1024, 2);
                            
                            echo "<tr>";
                            echo "<td>$server</td>";
                            echo "<td>" . number_format($stats['curr_items'] ?? 0) . "</td>";
                            echo "<td>" . number_format($stats['get_hits'] ?? 0) . "</td>";
                            echo "<td>" . number_format($stats['get_misses'] ?? 0) . "</td>";
                            echo "<td>{$memUsed} MB</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                    }
                    
                    // ทำความสะอาด
                    foreach ($testKeys as $key) {
                        $distributedCache->delete($key);
                    }
                    
                } catch (Exception $e) {
                    echo "<div class='warning'>";
                    echo "<h4>⚠️ Distributed Cache Warning:</h4>";
                    echo "<p>" . $e->getMessage() . "</p>";
                    echo "<p>This feature requires Memcached extension (not Memcache)</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>5. สรุปและเปรียบเทียบ</h2>
            <div class="output">
                <?php
                echo "<h3>5.1 Memcache vs Redis Comparison</h3>";
                
                echo "<div class='info'>";
                echo "<h4>📋 คุณสมบัติเปรียบเทียบ:</h4>";
                echo "<table>";
                echo "<tr><th>Feature</th><th>Memcache</th><th>Redis</th></tr>";
                echo "<tr><td>Data Types</td><td>String only</td><td>String, List, Set, Hash, etc.</td></tr>";
                echo "<tr><td>Persistence</td><td>No</td><td>Yes (RDB, AOF)</td></tr>";
                echo "<tr><td>Replication</td><td>No</td><td>Master-Slave</td></tr>";
                echo "<tr><td>Clustering</td><td>Client-side</td><td>Built-in</td></tr>";
                echo "<tr><td>Memory Usage</td><td>Lower</td><td>Higher</td></tr>";
                echo "<tr><td>Performance</td><td>Faster for simple ops</td><td>More versatile</td></tr>";
                echo "<tr><td>Use Case</td><td>Simple caching</td><td>Complex data structures</td></tr>";
                echo "</table>";
                echo "</div>";

                echo "<h3>5.2 Best Practices</h3>";
                
                echo "<div class='success'>";
                echo "<h4>✅ Memcache Best Practices:</h4>";
                echo "<ul>";
                echo "<li><strong>Key Design:</strong> ใช้ namespace และ version ใน key</li>";
                echo "<li><strong>TTL Strategy:</strong> กำหนด TTL ให้เหมาะสมกับประเภทข้อมูล</li>";
                echo "<li><strong>Compression:</strong> เปิด compression สำหรับข้อมูลขนาดใหญ่</li>";
                echo "<li><strong>Connection Pooling:</strong> ใช้ persistent connections</li>";
                echo "<li><strong>Error Handling:</strong> มี fallback เมื่อ cache ไม่พร้อมใช้งาน</li>";
                echo "<li><strong>Monitoring:</strong> ติดตาม hit rate และ memory usage</li>";
                echo "<li><strong>Serialization:</strong> เลือก serializer ที่เหมาะสม</li>";
                echo "</ul>";
                echo "</div>";
                
                echo "<div class='warning'>";
                echo "<h4>⚠️ ข้อควรระวัง:</h4>";
                echo "<ul>";
                echo "<li>ข้อมูลใน Memcache จะหายไปเมื่อ restart server</li>";
                echo "<li>ไม่มี built-in replication</li>";
                echo "<li>Limited data types (เฉพาะ string)</li>";
                echo "<li>ไม่มี atomic operations สำหรับ complex operations</li>";
                echo "<li>Memory limitation ตาม server configuration</li>";
                echo "</ul>";
                echo "</div>";

                // แสดงสถิติสุดท้าย
                $finalStats = $memcacheManager->getStats();
                if ($finalStats) {
                    echo "<h3>5.3 Final Statistics</h3>";
                    echo "<div class='stat'>Current Items<br><strong>" . number_format($finalStats['curr_items'] ?? 0) . "</strong></div>";
                    echo "<div class='stat'>Total Gets<br><strong>" . number_format($finalStats['cmd_get'] ?? 0) . "</strong></div>";
                    echo "<div class='stat'>Total Sets<br><strong>" . number_format($finalStats['cmd_set'] ?? 0) . "</strong></div>";
                    echo "<div class='stat'>Cache Hits<br><strong>" . number_format($finalStats['get_hits'] ?? 0) . "</strong></div>";
                    echo "<div class='stat'>Hit Rate<br><strong>" . 
                         (isset($finalStats['get_hits']) && isset($finalStats['get_misses']) && 
                          ($finalStats['get_hits'] + $finalStats['get_misses']) > 0 ?
                          number_format(($finalStats['get_hits'] / ($finalStats['get_hits'] + $finalStats['get_misses'])) * 100, 1) : '0') . "%</strong></div>";
                    echo "<div class='stat'>Memory Used<br><strong>" . 
                         number_format(($finalStats['bytes'] ?? 0) / 1024 / 1024, 2) . " MB</strong></div>";
                }
                ?>
            </div>
        </div>

        <div class="info">
            <h3>📝 สรุปความรู้ที่ได้เรียน:</h3>
            <ul>
                <li><strong>Memcache Connection:</strong> การเชื่อมต่อ Memcache/Memcached</li>
                <li><strong>Basic Operations:</strong> Set, Get, Delete, Increment, Decrement</li>
                <li><strong>Bulk Operations:</strong> setMultiple, getMultiple</li>
                <li><strong>TTL Management:</strong> การจัดการอายุของข้อมูล</li>
                <li><strong>Performance Optimization:</strong> Compression, Serialization</li>
                <li><strong>Cache Patterns:</strong> Cache-aside pattern implementation</li>
                <li><strong>Distributed Caching:</strong> การใช้งานแบบกระจาย</li>
                <li><strong>Monitoring:</strong> การติดตามสถิติและประสิทธิภาพ</li>
                <li><strong>Error Handling:</strong> การจัดการข้อผิดพลาด</li>
                <li><strong>Best Practices:</strong> แนวทางปฏิบัติที่ดี</li>
            </ul>
            
            <h3>🔗 ลิงก์ที่เกี่ยวข้อง:</h3>
            <p>
                <a href="../" class="btn">🏠 กลับหน้าหลัก</a>
                <a href="../lab03-redis/" class="btn">⬅️ Lab 03: Redis</a>
                <a href="../lab05-advanced/" class="btn">➡️ Lab 05: Advanced Techniques</a>
            </p>
        </div>
    </div>
</body>
</html>