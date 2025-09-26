<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 01: PHP พื้นฐาน - Arrays</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code-block { background-color: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
        pre { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #bdc3c7; padding: 8px; text-align: left; }
        th { background-color: #3498db; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 01: PHP พื้นฐาน - Arrays</h1>

        <div class="lab-section">
            <h2>1. Indexed Arrays</h2>
            <div class="output">
                <?php
                echo "<h3>1.1 การสร้าง Indexed Array</h3>";
                
                // วิธีที่ 1 - array()
                $fruits = array("แอปเปิ้ล", "ส้ม", "กล้วย", "มะม่วง");
                
                // วิธีที่ 2 - []
                $colors = ["แดง", "เขียว", "น้ำเงิน", "เหลือง"];
                
                // วิธีที่ 3 - กำหนดทีละตัว
                $numbers[0] = 10;
                $numbers[1] = 20;
                $numbers[2] = 30;
                $numbers[] = 40; // จะเป็น index 3 อัตโนมัติ

                echo "<p><strong>ผลไม้:</strong></p>";
                echo "<ul>";
                foreach($fruits as $index => $fruit) {
                    echo "<li>Index $index: $fruit</li>";
                }
                echo "</ul>";

                echo "<p><strong>สี:</strong></p>";
                echo "<ul>";
                for($i = 0; $i < count($colors); $i++) {
                    echo "<li>Index $i: " . $colors[$i] . "</li>";
                }
                echo "</ul>";

                echo "<p><strong>ตัวเลข:</strong></p>";
                echo "<ul>";
                foreach($numbers as $key => $value) {
                    echo "<li>Index $key: $value</li>";
                }
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>2. Associative Arrays</h2>
            <div class="output">
                <?php
                echo "<h3>2.1 การสร้าง Associative Array</h3>";
                
                $student = [
                    "name" => "สมชาย ใจดี",
                    "age" => 21,
                    "major" => "วิทยาการคอมพิวเตอร์",
                    "gpa" => 3.75,
                    "address" => "กรุงเทพมหานคร"
                ];

                $course_grades = array(
                    "PHP Programming" => "A",
                    "Database Systems" => "B+",
                    "Web Development" => "A-",
                    "Data Structures" => "B",
                    "Algorithms" => "A"
                );

                echo "<p><strong>ข้อมูลนักเรียน:</strong></p>";
                echo "<table>";
                echo "<tr><th>รายการ</th><th>ข้อมูล</th></tr>";
                foreach($student as $key => $value) {
                    echo "<tr><td>" . ucfirst($key) . "</td><td>$value</td></tr>";
                }
                echo "</table>";

                echo "<p><strong>เกรดในแต่ละวิชา:</strong></p>";
                echo "<table>";
                echo "<tr><th>วิชา</th><th>เกรด</th></tr>";
                foreach($course_grades as $course => $grade) {
                    echo "<tr><td>$course</td><td>$grade</td></tr>";
                }
                echo "</table>";

                echo "<h3>2.2 การเข้าถึงข้อมูลใน Associative Array</h3>";
                echo "<p><strong>ชื่อ:</strong> " . $student["name"] . "</p>";
                echo "<p><strong>อายุ:</strong> " . $student["age"] . " ปี</p>";
                echo "<p><strong>เกรดวิชา PHP:</strong> " . $course_grades["PHP Programming"] . "</p>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>3. Multidimensional Arrays</h2>
            <div class="output">
                <?php
                echo "<h3>3.1 Two-dimensional Array</h3>";
                
                $students = [
                    [
                        "name" => "สมชาย ใจดี",
                        "age" => 21,
                        "grades" => [85, 92, 78, 95]
                    ],
                    [
                        "name" => "สมหญิง รักเรียน",
                        "age" => 20,
                        "grades" => [90, 88, 92, 87]
                    ],
                    [
                        "name" => "สมศักดิ์ ขยัน",
                        "age" => 22,
                        "grades" => [78, 85, 90, 92]
                    ]
                ];

                echo "<p><strong>ข้อมูลนักเรียนทั้งหมด:</strong></p>";
                echo "<table>";
                echo "<tr><th>ชื่อ</th><th>อายุ</th><th>คะแนนเฉลี่ย</th><th>คะแนนรายวิชา</th></tr>";
                
                foreach($students as $student) {
                    $average = array_sum($student["grades"]) / count($student["grades"]);
                    echo "<tr>";
                    echo "<td>" . $student["name"] . "</td>";
                    echo "<td>" . $student["age"] . "</td>";
                    echo "<td>" . number_format($average, 2) . "</td>";
                    echo "<td>" . implode(", ", $student["grades"]) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                echo "<h3>3.2 Complex Multidimensional Array</h3>";
                
                $company = [
                    "name" => "บริษัท เทคโนโลยี จำกัด",
                    "departments" => [
                        "IT" => [
                            "manager" => "คุณสมชาย",
                            "employees" => [
                                ["name" => "นาย ก", "position" => "Programmer", "salary" => 35000],
                                ["name" => "นาง ข", "position" => "System Admin", "salary" => 40000]
                            ]
                        ],
                        "Marketing" => [
                            "manager" => "คุณสมหญิง",
                            "employees" => [
                                ["name" => "นาย ค", "position" => "Marketing Manager", "salary" => 45000],
                                ["name" => "นาง ง", "position" => "Sales Rep", "salary" => 30000]
                            ]
                        ]
                    ]
                ];

                echo "<p><strong>ข้อมูลบริษัท:</strong> " . $company["name"] . "</p>";
                
                foreach($company["departments"] as $dept_name => $dept_info) {
                    echo "<h4>แผนก: $dept_name</h4>";
                    echo "<p><strong>ผู้จัดการ:</strong> " . $dept_info["manager"] . "</p>";
                    echo "<p><strong>พนักงาน:</strong></p>";
                    echo "<ul>";
                    foreach($dept_info["employees"] as $employee) {
                        echo "<li>" . $employee["name"] . " - " . $employee["position"] . 
                             " (เงินเดือน: " . number_format($employee["salary"]) . " บาท)</li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>4. Array Functions</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 การนับและการวัดขนาด</h3>";
                
                $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                $fruits = ["แอปเปิ้ล", "ส้ม", "กล้วย", "มะม่วง", "ลิ้นจี่"];

                echo "<p><strong>Array \$numbers:</strong> " . implode(", ", $numbers) . "</p>";
                echo "<p><strong>จำนวนสมาชิก (count):</strong> " . count($numbers) . "</p>";
                echo "<p><strong>จำนวนสมาชิก (sizeof):</strong> " . sizeof($numbers) . "</p>";

                echo "<p><strong>Array \$fruits:</strong> " . implode(", ", $fruits) . "</p>";
                echo "<p><strong>จำนวนสมาชิก:</strong> " . count($fruits) . "</p>";

                echo "<h3>4.2 การค้นหาและการตรวจสอบ</h3>";
                
                echo "<p><strong>ค้นหาค่า '5' ใน numbers:</strong> " . (in_array(5, $numbers) ? "พบ" : "ไม่พบ") . "</p>";
                echo "<p><strong>ค้นหาค่า 'กล้วย' ใน fruits:</strong> " . (in_array("กล้วย", $fruits) ? "พบ" : "ไม่พบ") . "</p>";
                
                $search_key = array_search("มะม่วง", $fruits);
                echo "<p><strong>ตำแหน่งของ 'มะม่วง':</strong> " . ($search_key !== false ? $search_key : "ไม่พบ") . "</p>";

                echo "<p><strong>ตรวจสอบว่า key 'name' มีอยู่ใน student array:</strong> " . 
                     (array_key_exists("name", $student) ? "มี" : "ไม่มี") . "</p>";

                echo "<h3>4.3 การเรียงลำดับ</h3>";
                
                $unsorted_numbers = [64, 34, 25, 12, 22, 11, 90];
                $unsorted_fruits = ["มะม่วง", "แอปเปิ้ล", "กล้วย", "ส้ม"];

                echo "<p><strong>ตัวเลขก่อนเรียง:</strong> " . implode(", ", $unsorted_numbers) . "</p>";
                sort($unsorted_numbers);
                echo "<p><strong>ตัวเลขหลังเรียง (sort):</strong> " . implode(", ", $unsorted_numbers) . "</p>";

                $unsorted_numbers_desc = [64, 34, 25, 12, 22, 11, 90];
                rsort($unsorted_numbers_desc);
                echo "<p><strong>ตัวเลขเรียงจากมากไปน้อย (rsort):</strong> " . implode(", ", $unsorted_numbers_desc) . "</p>";

                echo "<p><strong>ผลไม้ก่อนเรียง:</strong> " . implode(", ", $unsorted_fruits) . "</p>";
                sort($unsorted_fruits);
                echo "<p><strong>ผลไม้หลังเรียง:</strong> " . implode(", ", $unsorted_fruits) . "</p>";

                echo "<h3>4.4 การรวมและการแยก</h3>";
                
                $array1 = [1, 2, 3];
                $array2 = [4, 5, 6];
                $merged = array_merge($array1, $array2);
                
                echo "<p><strong>Array 1:</strong> " . implode(", ", $array1) . "</p>";
                echo "<p><strong>Array 2:</strong> " . implode(", ", $array2) . "</p>";
                echo "<p><strong>รวม (array_merge):</strong> " . implode(", ", $merged) . "</p>";

                $slice_result = array_slice($merged, 2, 3);
                echo "<p><strong>ตัดจากตำแหน่ง 2 ขนาด 3 (array_slice):</strong> " . implode(", ", $slice_result) . "</p>";

                echo "<h3>4.5 การคำนวณ</h3>";
                
                $scores = [85, 92, 78, 95, 88, 91, 87];
                
                echo "<p><strong>คะแนน:</strong> " . implode(", ", $scores) . "</p>";
                echo "<p><strong>รวม (array_sum):</strong> " . array_sum($scores) . "</p>";
                echo "<p><strong>ค่าเฉลี่ย:</strong> " . number_format(array_sum($scores) / count($scores), 2) . "</p>";
                echo "<p><strong>คะแนนสูงสุด (max):</strong> " . max($scores) . "</p>";
                echo "<p><strong>คะแนนต่ำสุด (min):</strong> " . min($scores) . "</p>";

                echo "<h3>4.6 การแปลงเป็น String</h3>";
                
                $programming_languages = ["PHP", "JavaScript", "Python", "Java", "C++"];
                
                echo "<p><strong>ภาษาโปรแกรมมิ่ง:</strong> " . implode(", ", $programming_languages) . "</p>";
                echo "<p><strong>เชื่อมด้วย ' | ':</strong> " . implode(" | ", $programming_languages) . "</p>";
                echo "<p><strong>เชื่อมด้วย ' - ':</strong> " . implode(" - ", $programming_languages) . "</p>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>5. Array Iteration</h2>
            <div class="output">
                <?php
                echo "<h3>5.1 foreach Loop</h3>";
                
                $products = [
                    "laptop" => 25000,
                    "mouse" => 500,
                    "keyboard" => 1200,
                    "monitor" => 8000,
                    "headphones" => 2500
                ];

                echo "<p><strong>รายการสินค้า:</strong></p>";
                echo "<table>";
                echo "<tr><th>สินค้า</th><th>ราคา (บาท)</th></tr>";
                foreach($products as $product => $price) {
                    echo "<tr><td>$product</td><td>" . number_format($price) . "</td></tr>";
                }
                echo "</table>";

                echo "<h3>5.2 for Loop กับ Indexed Array</h3>";
                
                $months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน"];
                
                echo "<p><strong>เดือน (6 เดือนแรก):</strong></p>";
                echo "<ol>";
                for($i = 0; $i < count($months); $i++) {
                    echo "<li>" . $months[$i] . "</li>";
                }
                echo "</ol>";

                echo "<h3>5.3 while Loop กับ Array</h3>";
                
                $countdown = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1];
                $index = 0;
                
                echo "<p><strong>การนับถอยหลัง:</strong> ";
                while($index < count($countdown)) {
                    echo $countdown[$index];
                    if($index < count($countdown) - 1) echo ", ";
                    $index++;
                }
                echo " ลิ้งค์!</p>";

                echo "<h3>5.4 การใช้งาน array_map และ array_filter</h3>";
                
                $prices = [100, 250, 75, 300, 150, 500];
                
                // เพิ่มภาษี 7%
                $prices_with_tax = array_map(function($price) {
                    return $price * 1.07;
                }, $prices);

                // กรองสินค้าที่ราคาต่ำกว่า 200 บาท
                $cheap_items = array_filter($prices, function($price) {
                    return $price < 200;
                });

                echo "<p><strong>ราคาเดิม:</strong> " . implode(", ", $prices) . " บาท</p>";
                echo "<p><strong>ราคารวมภาษี 7%:</strong> " . 
                     implode(", ", array_map(function($p) { return number_format($p, 2); }, $prices_with_tax)) . " บาท</p>";
                echo "<p><strong>สินค้าราคาต่ำกว่า 200:</strong> " . implode(", ", $cheap_items) . " บาท</p>";
                ?>
            </div>
        </div>
    </div>
</body>
</html>