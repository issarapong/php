<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 01: PHP พื้นฐาน - Functions</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code-block { background-color: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
        pre { margin: 0; }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        .warning { color: #f39c12; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 01: PHP พื้นฐาน - Functions</h1>

        <div class="lab-section">
            <h2>1. Basic Functions</h2>
            <div class="output">
                <?php
                echo "<h3>1.1 ฟังก์ชันพื้นฐาน</h3>";
                
                // ฟังก์ชันทักทาย
                function sayHello() {
                    return "สวัสดีครับ! ยินดีต้อนรับสู่ PHP Programming";
                }

                // ฟังก์ชันที่มี parameter
                function greetUser($name) {
                    return "สวัสดี $name ยินดีที่ได้พบคุณครับ";
                }

                // ฟังก์ชันที่มี multiple parameters
                function introduce($name, $age, $city) {
                    return "ฉันชื่อ $name อายุ $age ปี อาศัยอยู่ที่ $city";
                }

                echo "<p>" . sayHello() . "</p>";
                echo "<p>" . greetUser("สมชาย") . "</p>";
                echo "<p>" . introduce("สมหญิง", 25, "กรุงเทพฯ") . "</p>";

                echo "<h3>1.2 ฟังก์ชันที่มี Default Parameters</h3>";
                
                function calculateTax($amount, $tax_rate = 0.07) {
                    $tax = $amount * $tax_rate;
                    $total = $amount + $tax;
                    return [
                        'amount' => $amount,
                        'tax' => $tax,
                        'total' => $total,
                        'tax_rate' => $tax_rate * 100
                    ];
                }

                $result1 = calculateTax(1000); // ใช้ tax rate default
                $result2 = calculateTax(1000, 0.10); // กำหนด tax rate เป็น 10%

                echo "<p><strong>คำนวณภาษี (default 7%):</strong></p>";
                echo "<ul>";
                echo "<li>ราคาสินค้า: " . number_format($result1['amount']) . " บาท</li>";
                echo "<li>ภาษี ({$result1['tax_rate']}%): " . number_format($result1['tax'], 2) . " บาท</li>";
                echo "<li>รวมทั้งหมด: " . number_format($result1['total'], 2) . " บาท</li>";
                echo "</ul>";

                echo "<p><strong>คำนวณภาษี (กำหนด 10%):</strong></p>";
                echo "<ul>";
                echo "<li>ราคาสินค้า: " . number_format($result2['amount']) . " บาท</li>";
                echo "<li>ภาษี ({$result2['tax_rate']}%): " . number_format($result2['tax'], 2) . " บาท</li>";
                echo "<li>รวมทั้งหมด: " . number_format($result2['total'], 2) . " บาท</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>2. Mathematical Functions</h2>
            <div class="output">
                <?php
                echo "<h3>2.1 ฟังก์ชันคำนวณทางคณิตศาสตร์</h3>";
                
                function add($a, $b) {
                    return $a + $b;
                }

                function subtract($a, $b) {
                    return $a - $b;
                }

                function multiply($a, $b) {
                    return $a * $b;
                }

                function divide($a, $b) {
                    if ($b == 0) {
                        return "Error: ไม่สามารถหารด้วยศูนย์ได้";
                    }
                    return $a / $b;
                }

                function power($base, $exponent) {
                    return pow($base, $exponent);
                }

                $num1 = 15;
                $num2 = 4;

                echo "<p><strong>คำนวณ $num1 และ $num2:</strong></p>";
                echo "<ul>";
                echo "<li>บวก: " . add($num1, $num2) . "</li>";
                echo "<li>ลบ: " . subtract($num1, $num2) . "</li>";
                echo "<li>คูณ: " . multiply($num1, $num2) . "</li>";
                echo "<li>หาร: " . divide($num1, $num2) . "</li>";
                echo "<li>$num1 ยกกำลัง $num2: " . power($num1, $num2) . "</li>";
                echo "</ul>";

                echo "<h3>2.2 ฟังก์ชันคำนวณขั้นสูง</h3>";
                
                function calculateCircleArea($radius) {
                    return pi() * pow($radius, 2);
                }

                function calculateRectangleArea($width, $height) {
                    return $width * $height;
                }

                function calculateTriangleArea($base, $height) {
                    return 0.5 * $base * $height;
                }

                function factorial($n) {
                    if ($n <= 1) return 1;
                    return $n * factorial($n - 1);
                }

                $radius = 5;
                $width = 10;
                $height = 8;
                $triangle_base = 12;
                $triangle_height = 6;
                $fact_num = 5;

                echo "<p><strong>การคำนวณพื้นที่:</strong></p>";
                echo "<ul>";
                echo "<li>วงกลม รัศมี $radius: " . number_format(calculateCircleArea($radius), 2) . " ตร.หน่วย</li>";
                echo "<li>สี่เหลี่ยม ${width}x${height}: " . calculateRectangleArea($width, $height) . " ตร.หน่วย</li>";
                echo "<li>สามเหลี่ยม ฐาน $triangle_base สูง $triangle_height: " . calculateTriangleArea($triangle_base, $triangle_height) . " ตร.หน่วย</li>";
                echo "</ul>";

                echo "<p><strong>Factorial $fact_num!:</strong> " . number_format(factorial($fact_num)) . "</p>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>3. String Functions</h2>
            <div class="output">
                <?php
                echo "<h3>3.1 ฟังก์ชันจัดการ String</h3>";
                
                function formatName($first_name, $last_name) {
                    return ucwords(strtolower($first_name . " " . $last_name));
                }

                function truncateString($string, $length = 50, $suffix = "...") {
                    if (strlen($string) <= $length) {
                        return $string;
                    }
                    return substr($string, 0, $length) . $suffix;
                }

                function countWords($text) {
                    return str_word_count($text);
                }

                function reverseString($string) {
                    return strrev($string);
                }

                function isPalindrome($string) {
                    $clean = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $string));
                    return $clean === strrev($clean);
                }

                $text = "นี่คือข้อความทดสอบสำหรับฟังก์ชันจัดการสตริงในภาษา PHP ซึ่งมีความยาวเกินกว่าที่เรากำหนดไว้";
                $first = "สมชาย";
                $last = "ใจดี";

                echo "<p><strong>การจัดรูปแบบชื่อ:</strong></p>";
                echo "<ul>";
                echo "<li>ชื่อ: $first $last</li>";
                echo "<li>จัดรูปแบบ: " . formatName($first, $last) . "</li>";
                echo "</ul>";

                echo "<p><strong>การตัดข้อความ:</strong></p>";
                echo "<ul>";
                echo "<li>ข้อความเต็ม: $text</li>";
                echo "<li>ตัดที่ 80 ตัวอักษร: " . truncateString($text, 80) . "</li>";
                echo "<li>ตัดที่ 50 ตัวอักษร: " . truncateString($text, 50, " [อ่านต่อ]") . "</li>";
                echo "</ul>";

                echo "<p><strong>การนับคำ:</strong> " . countWords($text) . " คำ</p>";

                $test_word = "radar";
                echo "<p><strong>ทดสอบ Palindrome:</strong></p>";
                echo "<ul>";
                echo "<li>'$test_word': " . (isPalindrome($test_word) ? "เป็น" : "ไม่เป็น") . " Palindrome</li>";
                echo "<li>'hello': " . (isPalindrome("hello") ? "เป็น" : "ไม่เป็น") . " Palindrome</li>";
                echo "<li>'A man a plan a canal Panama': " . (isPalindrome("A man a plan a canal Panama") ? "เป็น" : "ไม่เป็น") . " Palindrome</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>4. Array Functions</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 ฟังก์ชันจัดการ Array</h3>";
                
                function findMaxValue($array) {
                    return max($array);
                }

                function findMinValue($array) {
                    return min($array);
                }

                function calculateAverage($array) {
                    return array_sum($array) / count($array);
                }

                function removeEmptyValues($array) {
                    return array_filter($array, function($value) {
                        return !empty($value);
                    });
                }

                function sortStudentsByGrade($students) {
                    usort($students, function($a, $b) {
                        return $b['grade'] <=> $a['grade']; // เรียงจากมากไปน้อย
                    });
                    return $students;
                }

                $scores = [85, 92, 78, 95, 88, 73, 90, 87];
                $mixed_array = ["", "PHP", null, "MySQL", "", "JavaScript", 0, "HTML"];

                $students_list = [
                    ["name" => "สมชาย", "grade" => 85],
                    ["name" => "สมหญิง", "grade" => 92],
                    ["name" => "สมศักดิ์", "grade" => 78],
                    ["name" => "สมใจ", "grade" => 95]
                ];

                echo "<p><strong>คะแนนสอบ:</strong> " . implode(", ", $scores) . "</p>";
                echo "<ul>";
                echo "<li>คะแนนสูงสุด: " . findMaxValue($scores) . "</li>";
                echo "<li>คะแนนต่ำสุด: " . findMinValue($scores) . "</li>";
                echo "<li>คะแนนเฉลี่ย: " . number_format(calculateAverage($scores), 2) . "</li>";
                echo "</ul>";

                echo "<p><strong>Array ที่มีค่าว่าง:</strong></p>";
                echo "<ul>";
                echo "<li>ก่อนกรอง: " . print_r($mixed_array, true) . "</li>";
                echo "<li>หลังกรอง: " . print_r(removeEmptyValues($mixed_array), true) . "</li>";
                echo "</ul>";

                $sorted_students = sortStudentsByGrade($students_list);
                echo "<p><strong>เรียงนักเรียนตามคะแนน (สูง -> ต่ำ):</strong></p>";
                echo "<ol>";
                foreach($sorted_students as $student) {
                    echo "<li>" . $student["name"] . " - " . $student["grade"] . " คะแนน</li>";
                }
                echo "</ol>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>5. Date and Time Functions</h2>
            <div class="output">
                <?php
                echo "<h3>5.1 ฟังก์ชันวันที่และเวลา</h3>";
                
                function getCurrentDateTime() {
                    return date("Y-m-d H:i:s");
                }

                function formatThaiDate($timestamp = null) {
                    if ($timestamp === null) {
                        $timestamp = time();
                    }
                    
                    $thai_months = [
                        1 => "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                    ];
                    
                    $day = date("j", $timestamp);
                    $month = $thai_months[date("n", $timestamp)];
                    $year = date("Y", $timestamp) + 543;
                    
                    return "$day $month $year";
                }

                function calculateAge($birth_date) {
                    $birth = new DateTime($birth_date);
                    $today = new DateTime();
                    $age = $today->diff($birth);
                    return $age->y;
                }

                function addDaysToDate($date, $days) {
                    $timestamp = strtotime($date);
                    $new_timestamp = $timestamp + ($days * 24 * 60 * 60);
                    return date("Y-m-d", $new_timestamp);
                }

                function isWeekend($date) {
                    $day_of_week = date("N", strtotime($date));
                    return $day_of_week >= 6; // 6 = Saturday, 7 = Sunday
                }

                $current_time = time();
                $birth_date = "1999-03-15";
                $test_date = "2024-01-27"; // Saturday

                echo "<p><strong>วันเวลาปัจจุบัน:</strong></p>";
                echo "<ul>";
                echo "<li>รูปแบบ ISO: " . getCurrentDateTime() . "</li>";
                echo "<li>รูปแบบไทย: " . formatThaiDate() . "</li>";
                echo "<li>Timestamp: " . $current_time . "</li>";
                echo "</ul>";

                echo "<p><strong>การคำนวณอายุ:</strong></p>";
                echo "<ul>";
                echo "<li>วันเกิด: " . formatThaiDate(strtotime($birth_date)) . "</li>";
                echo "<li>อายุ: " . calculateAge($birth_date) . " ปี</li>";
                echo "</ul>";

                echo "<p><strong>การบวกลบวันที่:</strong></p>";
                echo "<ul>";
                echo "<li>วันนี้: " . date("Y-m-d") . "</li>";
                echo "<li>บวก 30 วัน: " . addDaysToDate(date("Y-m-d"), 30) . "</li>";
                echo "<li>ลบ 15 วัน: " . addDaysToDate(date("Y-m-d"), -15) . "</li>";
                echo "</ul>";

                echo "<p><strong>ตรวจสอบวันหยุด:</strong></p>";
                echo "<ul>";
                echo "<li>วันที่ $test_date: " . (isWeekend($test_date) ? "วันหยุดสุดสัปดาห์" : "วันทำงาน") . "</li>";
                echo "<li>วันนี้: " . (isWeekend(date("Y-m-d")) ? "วันหยุดสุดสัปดาห์" : "วันทำงาน") . "</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>6. Variable Functions และ Anonymous Functions</h2>
            <div class="output">
                <?php
                echo "<h3>6.1 Variable Functions</h3>";
                
                function operation1($a, $b) {
                    return $a + $b;
                }

                function operation2($a, $b) {
                    return $a * $b;
                }

                $func_name = "operation1";
                echo "<p><strong>Variable Function:</strong></p>";
                echo "<ul>";
                echo "<li>เรียกใช้ \$func_name('operation1'): " . $func_name(5, 3) . "</li>";

                $func_name = "operation2";
                echo "<li>เรียกใช้ \$func_name('operation2'): " . $func_name(5, 3) . "</li>";
                echo "</ul>";

                echo "<h3>6.2 Anonymous Functions (Closures)</h3>";
                
                $multiply_by_two = function($number) {
                    return $number * 2;
                };

                $calculate_discount = function($price, $discount_percent) {
                    return $price - ($price * $discount_percent / 100);
                };

                echo "<p><strong>Anonymous Functions:</strong></p>";
                echo "<ul>";
                echo "<li>คูณ 10 ด้วย 2: " . $multiply_by_two(10) . "</li>";
                echo "<li>ส่วนลด 1000 บาท 15%: " . number_format($calculate_discount(1000, 15), 2) . " บาท</li>";
                echo "</ul>";

                echo "<h3>6.3 Array Functions กับ Closures</h3>";
                
                $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

                $squared = array_map(function($n) { return $n * $n; }, $numbers);
                $even_numbers = array_filter($numbers, function($n) { return $n % 2 == 0; });
                
                echo "<p><strong>เลขกำลัง 2:</strong> " . implode(", ", $squared) . "</p>";
                echo "<p><strong>เลขคู่:</strong> " . implode(", ", $even_numbers) . "</p>";

                // ฟังก์ชันที่ return ฟังก์ชัน
                function createMultiplier($multiplier) {
                    return function($number) use ($multiplier) {
                        return $number * $multiplier;
                    };
                }

                $times_three = createMultiplier(3);
                $times_five = createMultiplier(5);

                echo "<p><strong>Higher-order Functions:</strong></p>";
                echo "<ul>";
                echo "<li>7 คูณ 3: " . $times_three(7) . "</li>";
                echo "<li>8 คูณ 5: " . $times_five(8) . "</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>7. Error Handling ในฟังก์ชัน</h2>
            <div class="output">
                <?php
                echo "<h3>7.1 การจัดการ Error ในฟังก์ชัน</h3>";
                
                function safeDivide($dividend, $divisor) {
                    if ($divisor == 0) {
                        return [
                            'success' => false,
                            'error' => 'ไม่สามารถหารด้วยศูนย์ได้',
                            'result' => null
                        ];
                    }
                    
                    return [
                        'success' => true,
                        'error' => null,
                        'result' => $dividend / $divisor
                    ];
                }

                function validateEmail($email) {
                    if (empty($email)) {
                        return [
                            'valid' => false,
                            'message' => 'กรุณาใส่อีเมล'
                        ];
                    }
                    
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return [
                            'valid' => false,
                            'message' => 'รูปแบบอีเมลไม่ถูกต้อง'
                        ];
                    }
                    
                    return [
                        'valid' => true,
                        'message' => 'อีเมลถูกต้อง'
                    ];
                }

                // ทดสอบ safeDivide
                $result1 = safeDivide(10, 2);
                $result2 = safeDivide(10, 0);

                echo "<p><strong>การหารที่ปลอดภัย:</strong></p>";
                echo "<ul>";
                echo "<li>10 ÷ 2: ";
                if ($result1['success']) {
                    echo "<span class='success'>สำเร็จ - ผลลัพธ์: " . $result1['result'] . "</span>";
                } else {
                    echo "<span class='error'>ผิดพลาด - " . $result1['error'] . "</span>";
                }
                echo "</li>";

                echo "<li>10 ÷ 0: ";
                if ($result2['success']) {
                    echo "<span class='success'>สำเร็จ - ผลลัพธ์: " . $result2['result'] . "</span>";
                } else {
                    echo "<span class='error'>ผิดพลาด - " . $result2['error'] . "</span>";
                }
                echo "</li>";
                echo "</ul>";

                // ทดสอบ validateEmail
                $emails = ['test@example.com', 'invalid-email', '', 'user@domain.co.th'];

                echo "<p><strong>การตรวจสอบอีเมล:</strong></p>";
                echo "<ul>";
                foreach ($emails as $email) {
                    $validation = validateEmail($email);
                    $display_email = empty($email) ? '[ว่าง]' : $email;
                    
                    echo "<li>$display_email: ";
                    if ($validation['valid']) {
                        echo "<span class='success'>" . $validation['message'] . "</span>";
                    } else {
                        echo "<span class='error'>" . $validation['message'] . "</span>";
                    }
                    echo "</li>";
                }
                echo "</ul>";
                ?>
            </div>
        </div>
    </div>
</body>
</html>