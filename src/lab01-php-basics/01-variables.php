<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 01: PHP พื้นฐาน - Variables และ Data Types</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code-block { background-color: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
        pre { margin: 0; }
        .highlight { background-color: #f1c40f; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 01: PHP พื้นฐาน - Variables และ Data Types</h1>

        <div class="lab-section">
            <h2>1. การประกาศตัวแปรและ Data Types</h2>
            <div class="code-block">
                <pre><?php echo htmlspecialchars('<?php'); ?></pre>
            </div>
            <div class="output">
                <?php
                // 1. String Variables
                echo "<h3>1.1 String Variables</h3>";
                $student_name = "สมชาย ใจดี";
                $university = "มหาวิทยาลยเทคโนโลยีแห่งชาติ";
                $greeting = "สวัสดีครับ";

                echo "<p><strong>ชื่อนักเรียน:</strong> $student_name</p>";
                echo "<p><strong>มหาวิทยาลัย:</strong> $university</p>";
                echo "<p><strong>คำทักทาย:</strong> $greeting</p>";

                // 2. Integer Variables
                echo "<h3>1.2 Integer Variables</h3>";
                $age = 25;
                $year = 2024;
                $student_count = 150;

                echo "<p><strong>อายุ:</strong> $age ปี</p>";
                echo "<p><strong>ปี:</strong> $year</p>";
                echo "<p><strong>จำนวนนักเรียน:</strong> $student_count คน</p>";

                // 3. Float Variables
                echo "<h3>1.3 Float Variables</h3>";
                $price = 299.50;
                $discount = 10.5;
                $final_price = $price - ($price * $discount / 100);

                echo "<p><strong>ราคาเดิม:</strong> $price บาท</p>";
                echo "<p><strong>ส่วนลด:</strong> $discount%</p>";
                echo "<p><strong>ราคาหลังส่วนลด:</strong> " . number_format($final_price, 2) . " บาท</p>";

                // 4. Boolean Variables
                echo "<h3>1.4 Boolean Variables</h3>";
                $is_student = true;
                $is_graduated = false;

                echo "<p><strong>เป็นนักเรียน:</strong> " . ($is_student ? "ใช่" : "ไม่ใช่") . "</p>";
                echo "<p><strong>จบการศึกษาแล้ว:</strong> " . ($is_graduated ? "ใช่" : "ไม่ใช่") . "</p>";

                // 5. Array Variables
                echo "<h3>1.5 Array Variables</h3>";
                $subjects = array("PHP", "MySQL", "JavaScript", "HTML", "CSS");
                $scores = [85, 92, 78, 95, 88];

                echo "<p><strong>วิชาที่เรียน:</strong></p>";
                echo "<ul>";
                foreach($subjects as $subject) {
                    echo "<li>$subject</li>";
                }
                echo "</ul>";

                echo "<p><strong>คะแนนสอบ:</strong></p>";
                echo "<ul>";
                foreach($scores as $index => $score) {
                    echo "<li>" . $subjects[$index] . ": $score คะแนน</li>";
                }
                echo "</ul>";

                // 6. Associative Array
                echo "<h3>1.6 Associative Array</h3>";
                $student_info = [
                    "name" => "สมชาย ใจดี",
                    "age" => 25,
                    "major" => "Computer Science",
                    "gpa" => 3.75,
                    "is_scholarship" => true
                ];

                echo "<p><strong>ข้อมูลนักเรียน:</strong></p>";
                echo "<ul>";
                echo "<li>ชื่อ: " . $student_info["name"] . "</li>";
                echo "<li>อายุ: " . $student_info["age"] . " ปี</li>";
                echo "<li>สาขา: " . $student_info["major"] . "</li>";
                echo "<li>เกรดเฉลี่ย: " . $student_info["gpa"] . "</li>";
                echo "<li>ทุนการศึกษา: " . ($student_info["is_scholarship"] ? "มี" : "ไม่มี") . "</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>2. การใช้งาน var_dump() และ gettype()</h2>
            <div class="output">
                <?php
                echo "<h3>2.1 ตรวจสอบชนิดข้อมูล</h3>";
                
                $test_string = "Hello World";
                $test_int = 42;
                $test_float = 3.14159;
                $test_bool = true;
                $test_array = [1, 2, 3, 4, 5];
                $test_null = null;

                echo "<p><strong>String:</strong> '$test_string' - ชนิด: " . gettype($test_string) . "</p>";
                echo "<p><strong>Integer:</strong> $test_int - ชนิด: " . gettype($test_int) . "</p>";
                echo "<p><strong>Float:</strong> $test_float - ชนิด: " . gettype($test_float) . "</p>";
                echo "<p><strong>Boolean:</strong> " . ($test_bool ? "true" : "false") . " - ชนิด: " . gettype($test_bool) . "</p>";
                echo "<p><strong>Array:</strong> " . print_r($test_array, true) . " - ชนิด: " . gettype($test_array) . "</p>";
                echo "<p><strong>Null:</strong> null - ชนิด: " . gettype($test_null) . "</p>";

                echo "<h3>2.2 var_dump() รายละเอียด</h3>";
                echo "<div class='code-block'>";
                echo "<pre>";
                var_dump($student_info);
                echo "</pre>";
                echo "</div>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>3. String Operations</h2>
            <div class="output">
                <?php
                echo "<h3>3.1 การต่อ String</h3>";
                $first_name = "สมชาย";
                $last_name = "ใจดี";
                $full_name = $first_name . " " . $last_name;

                echo "<p><strong>ชื่อ:</strong> $first_name</p>";
                echo "<p><strong>นามสกุล:</strong> $last_name</p>";
                echo "<p><strong>ชื่อเต็ม:</strong> $full_name</p>";

                echo "<h3>3.2 String Functions</h3>";
                $message = "ยินดีต้อนรับสู่ PHP Programming";
                
                echo "<p><strong>ข้อความต้นฉบับ:</strong> $message</p>";
                echo "<p><strong>ความยาว:</strong> " . strlen($message) . " ตัวอักษร</p>";
                echo "<p><strong>ตัวพิมพ์ใหญ่:</strong> " . strtoupper($message) . "</p>";
                echo "<p><strong>ตัวพิมพ์เล็ก:</strong> " . strtolower($message) . "</p>";
                echo "<p><strong>ตัวอักษรตัวแรกพิมพ์ใหญ่:</strong> " . ucfirst($message) . "</p>";
                echo "<p><strong>ทุกคำขึ้นต้นด้วยตัวพิมพ์ใหญ่:</strong> " . ucwords($message) . "</p>";

                echo "<h3>3.3 String Replace</h3>";
                $old_text = "PHP เป็นภาษาโปรแกรมมิ่งที่ยาก";
                $new_text = str_replace("ยาก", "ง่าย", $old_text);
                
                echo "<p><strong>ข้อความเดิม:</strong> $old_text</p>";
                echo "<p><strong>ข้อความใหม่:</strong> $new_text</p>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>4. Mathematical Operations</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 การคำนวณพื้นฐาน</h3>";
                $num1 = 15;
                $num2 = 4;

                echo "<p><strong>จำนวนที่ 1:</strong> $num1</p>";
                echo "<p><strong>จำนวนที่ 2:</strong> $num2</p>";
                echo "<p><strong>การบวก (+):</strong> $num1 + $num2 = " . ($num1 + $num2) . "</p>";
                echo "<p><strong>การลบ (-):</strong> $num1 - $num2 = " . ($num1 - $num2) . "</p>";
                echo "<p><strong>การคูณ (*):</strong> $num1 * $num2 = " . ($num1 * $num2) . "</p>";
                echo "<p><strong>การหาร (/):</strong> $num1 / $num2 = " . ($num1 / $num2) . "</p>";
                echo "<p><strong>หารเอาเศษ (%):</strong> $num1 % $num2 = " . ($num1 % $num2) . "</p>";
                echo "<p><strong>ยกกำลัง (**):</strong> $num1 ** 2 = " . ($num1 ** 2) . "</p>";

                echo "<h3>4.2 Math Functions</h3>";
                $number = 16.7;
                
                echo "<p><strong>จำนวน:</strong> $number</p>";
                echo "<p><strong>รากที่ 2:</strong> " . sqrt($number) . "</p>";
                echo "<p><strong>ปัดขึ้น:</strong> " . ceil($number) . "</p>";
                echo "<p><strong>ปัดลง:</strong> " . floor($number) . "</p>";
                echo "<p><strong>ปัดเศษ:</strong> " . round($number) . "</p>";
                echo "<p><strong>ค่าสัมบูรณ์:</strong> " . abs(-$number) . "</p>";
                echo "<p><strong>เลขสุ่ม (1-100):</strong> " . rand(1, 100) . "</p>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>5. Constants</h2>
            <div class="output">
                <?php
                echo "<h3>5.1 การประกาศและใช้งาน Constants</h3>";
                
                // การประกาศ constants
                define("SITE_NAME", "PHP Learning Lab");
                define("VERSION", "1.0.0");
                define("MAX_USERS", 1000);
                const TAX_RATE = 0.07;

                echo "<p><strong>ชื่อเว็บไซต์:</strong> " . SITE_NAME . "</p>";
                echo "<p><strong>เวอร์ชั่น:</strong> " . VERSION . "</p>";
                echo "<p><strong>ผู้ใช้สูงสุด:</strong> " . MAX_USERS . " คน</p>";
                echo "<p><strong>อัตราภาษี:</strong> " . (TAX_RATE * 100) . "%</p>";

                echo "<h3>5.2 Predefined Constants</h3>";
                echo "<p><strong>เวอร์ชั่น PHP:</strong> " . PHP_VERSION . "</p>";
                echo "<p><strong>ชื่อไฟล์:</strong> " . __FILE__ . "</p>";
                echo "<p><strong>บรรทัดปัจจุบัน:</strong> " . __LINE__ . "</p>";
                echo "<p><strong>ระบบปฏิบัติการ:</strong> " . PHP_OS . "</p>";

                echo "<h3>5.3 การคำนวณด้วย Constants</h3>";
                $price = 1000;
                $tax = $price * TAX_RATE;
                $total = $price + $tax;

                echo "<p><strong>ราคาสินค้า:</strong> " . number_format($price, 2) . " บาท</p>";
                echo "<p><strong>ภาษี (" . (TAX_RATE * 100) . "%):</strong> " . number_format($tax, 2) . " บาท</p>";
                echo "<p><strong>ราคารวม:</strong> " . number_format($total, 2) . " บาท</p>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>6. Variable Scope</h2>
            <div class="output">
                <?php
                echo "<h3>6.1 Global และ Local Variables</h3>";
                
                $global_var = "ฉันเป็น Global Variable";

                function testScope() {
                    global $global_var;
                    $local_var = "ฉันเป็น Local Variable";
                    
                    echo "<p><strong>ภายในฟังก์ชัน - Global:</strong> $global_var</p>";
                    echo "<p><strong>ภายในฟังก์ชัน - Local:</strong> $local_var</p>";
                }

                echo "<p><strong>นอกฟังก์ชัน - Global:</strong> $global_var</p>";
                testScope();

                echo "<h3>6.2 Static Variables</h3>";
                
                function counterFunction() {
                    static $count = 0;
                    $count++;
                    echo "<p><strong>การเรียกครั้งที่:</strong> $count</p>";
                }

                counterFunction();
                counterFunction();
                counterFunction();

                echo "<h3>6.3 Superglobals</h3>";
                echo "<p><strong>\$_SERVER['SERVER_NAME']:</strong> " . $_SERVER['SERVER_NAME'] . "</p>";
                echo "<p><strong>\$_SERVER['REQUEST_METHOD']:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
                echo "<p><strong>\$_SERVER['HTTP_HOST']:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
                ?>
            </div>
        </div>
    </div>
</body>
</html>