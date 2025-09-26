<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 01: PHP พื้นฐาน - Object Oriented Programming</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code-block { background-color: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
        pre { margin: 0; }
        .highlight { background-color: #f1c40f; padding: 2px 5px; border-radius: 3px; }
        .info { background-color: #d5dbdb; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 01: PHP พื้นฐาน - Object Oriented Programming</h1>

        <div class="lab-section">
            <h2>1. Basic Class และ Object</h2>
            <div class="output">
                <?php
                echo "<h3>1.1 การสร้าง Class และ Object</h3>";
                
                class Student {
                    // Properties (ตัวแปรของ class)
                    public $name;
                    public $age;
                    public $major;
                    public $gpa;
                    
                    // Constructor
                    public function __construct($name, $age, $major, $gpa = 0.0) {
                        $this->name = $name;
                        $this->age = $age;
                        $this->major = $major;
                        $this->gpa = $gpa;
                    }
                    
                    // Methods (ฟังก์ชันของ class)
                    public function introduce() {
                        return "สวัสดีครับ ผม{$this->name} อายุ {$this->age} ปี เรียนสาขา {$this->major}";
                    }
                    
                    public function getGradeLevel() {
                        if ($this->gpa >= 3.50) {
                            return "เกรดดีเยี่ยม";
                        } elseif ($this->gpa >= 3.00) {
                            return "เกรดดี";
                        } elseif ($this->gpa >= 2.50) {
                            return "เกรดปานกลาง";
                        } elseif ($this->gpa >= 2.00) {
                            return "เกรดอ่อน";
                        } else {
                            return "เกรดต่ำ";
                        }
                    }
                    
                    public function updateGPA($new_gpa) {
                        if ($new_gpa >= 0.0 && $new_gpa <= 4.0) {
                            $this->gpa = $new_gpa;
                            return "อัพเดท GPA เรียบร้อย";
                        } else {
                            return "GPA ต้องอยู่ระหว่าง 0.0 - 4.0";
                        }
                    }
                    
                    public function getStudentInfo() {
                        return [
                            'name' => $this->name,
                            'age' => $this->age,
                            'major' => $this->major,
                            'gpa' => $this->gpa,
                            'grade_level' => $this->getGradeLevel()
                        ];
                    }
                }

                // การสร้าง objects
                $student1 = new Student("สมชาย ใจดี", 20, "วิทยาการคอมพิวเตอร์", 3.75);
                $student2 = new Student("สมหญิง รักเรียน", 21, "วิศวกรรมซอฟต์แวร์", 3.25);
                $student3 = new Student("สมศักดิ์ ขยัน", 19, "เทคโนโลยีสารสนเทศ");

                echo "<p><strong>การแนะนำตัว:</strong></p>";
                echo "<ul>";
                echo "<li>" . $student1->introduce() . "</li>";
                echo "<li>" . $student2->introduce() . "</li>";
                echo "<li>" . $student3->introduce() . "</li>";
                echo "</ul>";

                echo "<p><strong>ข้อมูลนักเรียน:</strong></p>";
                $students = [$student1, $student2, $student3];
                
                echo "<table style='width: 100%; border-collapse: collapse;'>";
                echo "<tr style='background-color: #3498db; color: white;'>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>ชื่อ</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>อายุ</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>สาขา</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>GPA</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>ระดับเกรด</th>";
                echo "</tr>";
                
                foreach ($students as $student) {
                    $info = $student->getStudentInfo();
                    echo "<tr>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['name'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['age'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['major'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['gpa'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['grade_level'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                // อัพเดท GPA
                echo "<p><strong>การอัพเดท GPA:</strong></p>";
                echo "<ul>";
                echo "<li>" . $student3->updateGPA(2.75) . "</li>";
                echo "<li>GPA ใหม่ของ " . $student3->name . ": " . $student3->gpa . " (" . $student3->getGradeLevel() . ")</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>2. Property Visibility (public, private, protected)</h2>
            <div class="output">
                <?php
                echo "<h3>2.1 การใช้งาน Visibility Modifiers</h3>";
                
                class BankAccount {
                    private $account_number;
                    private $balance;
                    protected $account_type;
                    public $owner_name;
                    
                    public function __construct($account_number, $owner_name, $account_type = "Savings") {
                        $this->account_number = $account_number;
                        $this->owner_name = $owner_name;
                        $this->account_type = $account_type;
                        $this->balance = 0.0;
                    }
                    
                    // Getter methods
                    public function getAccountNumber() {
                        // ซ่อนเลขบัญชีบางส่วนเพื่อความปลอดภัย
                        return "***-" . substr($this->account_number, -4);
                    }
                    
                    public function getBalance() {
                        return $this->balance;
                    }
                    
                    public function getAccountType() {
                        return $this->account_type;
                    }
                    
                    // Setter methods
                    public function deposit($amount) {
                        if ($amount > 0) {
                            $this->balance += $amount;
                            return "ฝากเงิน " . number_format($amount, 2) . " บาท สำเร็จ";
                        } else {
                            return "จำนวนเงินต้องมากกว่า 0";
                        }
                    }
                    
                    public function withdraw($amount) {
                        if ($amount > 0 && $amount <= $this->balance) {
                            $this->balance -= $amount;
                            return "ถอนเงิน " . number_format($amount, 2) . " บาท สำเร็จ";
                        } elseif ($amount > $this->balance) {
                            return "เงินในบัญชีไม่เพียงพอ";
                        } else {
                            return "จำนวนเงินต้องมากกว่า 0";
                        }
                    }
                    
                    public function transfer($to_account, $amount) {
                        if ($amount > 0 && $amount <= $this->balance) {
                            $this->balance -= $amount;
                            $to_account->balance += $amount;
                            return "โอนเงิน " . number_format($amount, 2) . " บาท ไปยัง " . $to_account->owner_name . " สำเร็จ";
                        } elseif ($amount > $this->balance) {
                            return "เงินในบัญชีไม่เพียงพอสำหรับการโอน";
                        } else {
                            return "จำนวนเงินต้องมากกว่า 0";
                        }
                    }
                    
                    public function getAccountSummary() {
                        return [
                            'account_number' => $this->getAccountNumber(),
                            'owner' => $this->owner_name,
                            'type' => $this->account_type,
                            'balance' => $this->balance
                        ];
                    }
                }

                // สร้างบัญชีธนาคาร
                $account1 = new BankAccount("1234567890", "สมชาย ใจดี", "Savings");
                $account2 = new BankAccount("9876543210", "สมหญิง รักเงิน", "Current");

                echo "<p><strong>การทำรายการธนาคาร:</strong></p>";
                echo "<ul>";
                echo "<li>" . $account1->deposit(10000) . "</li>";
                echo "<li>" . $account1->withdraw(2500) . "</li>";
                echo "<li>" . $account2->deposit(5000) . "</li>";
                echo "<li>" . $account1->transfer($account2, 1000) . "</li>";
                echo "</ul>";

                echo "<p><strong>สรุปบัญชี:</strong></p>";
                $accounts = [$account1, $account2];
                
                echo "<table style='width: 100%; border-collapse: collapse;'>";
                echo "<tr style='background-color: #3498db; color: white;'>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>เลขบัญชี</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>เจ้าของ</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>ประเภท</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>ยอดเงิน (บาท)</th>";
                echo "</tr>";
                
                foreach ($accounts as $account) {
                    $summary = $account->getAccountSummary();
                    echo "<tr>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $summary['account_number'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $summary['owner'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $summary['type'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px; text-align: right;'>" . number_format($summary['balance'], 2) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>3. Static Properties และ Methods</h2>
            <div class="output">
                <?php
                echo "<h3>3.1 Static Properties และ Methods</h3>";
                
                class MathUtils {
                    public static $pi = 3.14159;
                    private static $calculation_count = 0;
                    
                    public static function add($a, $b) {
                        self::$calculation_count++;
                        return $a + $b;
                    }
                    
                    public static function multiply($a, $b) {
                        self::$calculation_count++;
                        return $a * $b;
                    }
                    
                    public static function calculateCircleArea($radius) {
                        self::$calculation_count++;
                        return self::$pi * pow($radius, 2);
                    }
                    
                    public static function calculateCircleCircumference($radius) {
                        self::$calculation_count++;
                        return 2 * self::$pi * $radius;
                    }
                    
                    public static function getCalculationCount() {
                        return self::$calculation_count;
                    }
                    
                    public static function resetCalculationCount() {
                        self::$calculation_count = 0;
                        return "รีเซ็ตตัวนับการคำนวณเรียบร้อย";
                    }
                }

                class Counter {
                    private static $count = 0;
                    
                    public static function increment() {
                        self::$count++;
                    }
                    
                    public static function decrement() {
                        if (self::$count > 0) {
                            self::$count--;
                        }
                    }
                    
                    public static function getCount() {
                        return self::$count;
                    }
                    
                    public static function reset() {
                        self::$count = 0;
                    }
                }

                // การใช้งาน Static Methods
                echo "<p><strong>การใช้งาน MathUtils:</strong></p>";
                echo "<ul>";
                echo "<li>5 + 3 = " . MathUtils::add(5, 3) . "</li>";
                echo "<li>7 × 4 = " . MathUtils::multiply(7, 4) . "</li>";
                echo "<li>พื้นที่วงกลม รัศมี 5 = " . number_format(MathUtils::calculateCircleArea(5), 2) . "</li>";
                echo "<li>เส้นรอบวงกลม รัศมี 5 = " . number_format(MathUtils::calculateCircleCircumference(5), 2) . "</li>";
                echo "<li>จำนวนการคำนวณทั้งหมด: " . MathUtils::getCalculationCount() . " ครั้ง</li>";
                echo "</ul>";

                echo "<p><strong>การใช้งาน Counter:</strong></p>";
                echo "<ul>";
                Counter::increment();
                Counter::increment();
                Counter::increment();
                echo "<li>หลังเพิ่ม 3 ครั้ง: " . Counter::getCount() . "</li>";

                Counter::decrement();
                echo "<li>หลังลด 1 ครั้ง: " . Counter::getCount() . "</li>";

                Counter::reset();
                echo "<li>หลังรีเซ็ต: " . Counter::getCount() . "</li>";
                echo "</ul>";

                echo "<p><strong>Static Property:</strong></p>";
                echo "<ul>";
                echo "<li>ค่า PI: " . MathUtils::$pi . "</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>4. Class Inheritance (การสืบทอด)</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 การสืบทอด Class</h3>";
                
                class Animal {
                    protected $name;
                    protected $species;
                    protected $age;
                    
                    public function __construct($name, $species, $age) {
                        $this->name = $name;
                        $this->species = $species;
                        $this->age = $age;
                    }
                    
                    public function getName() {
                        return $this->name;
                    }
                    
                    public function getAge() {
                        return $this->age;
                    }
                    
                    public function makeSound() {
                        return $this->name . " ส่งเสียง";
                    }
                    
                    public function eat() {
                        return $this->name . " กำลังกินอาหาร";
                    }
                    
                    public function sleep() {
                        return $this->name . " กำลังนอนหลับ";
                    }
                    
                    public function getInfo() {
                        return [
                            'name' => $this->name,
                            'species' => $this->species,
                            'age' => $this->age
                        ];
                    }
                }

                class Dog extends Animal {
                    private $breed;
                    private $is_trained;
                    
                    public function __construct($name, $age, $breed, $is_trained = false) {
                        parent::__construct($name, "Dog", $age);
                        $this->breed = $breed;
                        $this->is_trained = $is_trained;
                    }
                    
                    public function makeSound() {
                        return $this->name . " เห่า: โฮ่ง โฮ่ง!";
                    }
                    
                    public function fetch() {
                        return $this->name . " วิ่งไปเก็บของมาให้";
                    }
                    
                    public function wagTail() {
                        return $this->name . " แกว่งหาง";
                    }
                    
                    public function train() {
                        if (!$this->is_trained) {
                            $this->is_trained = true;
                            return $this->name . " ผ่านการฝึกเรียบร้อย";
                        } else {
                            return $this->name . " ได้รับการฝึกมาแล้ว";
                        }
                    }
                    
                    public function getInfo() {
                        $info = parent::getInfo();
                        $info['breed'] = $this->breed;
                        $info['is_trained'] = $this->is_trained;
                        return $info;
                    }
                }

                class Cat extends Animal {
                    private $color;
                    private $is_indoor;
                    
                    public function __construct($name, $age, $color, $is_indoor = true) {
                        parent::__construct($name, "Cat", $age);
                        $this->color = $color;
                        $this->is_indoor = $is_indoor;
                    }
                    
                    public function makeSound() {
                        return $this->name . " ร้อง: เมี้ยว เมี้ยว!";
                    }
                    
                    public function purr() {
                        return $this->name . " ส่งเสียงครืด";
                    }
                    
                    public function climb() {
                        return $this->name . " ปีนต้นไม้";
                    }
                    
                    public function hunt() {
                        if (!$this->is_indoor) {
                            return $this->name . " ออกไปล่าหนู";
                        } else {
                            return $this->name . " เล่นกับของเล่นในบ้าน";
                        }
                    }
                    
                    public function getInfo() {
                        $info = parent::getInfo();
                        $info['color'] = $this->color;
                        $info['is_indoor'] = $this->is_indoor;
                        return $info;
                    }
                }

                // สร้าง objects
                $dog1 = new Dog("บัดดี้", 3, "Golden Retriever", true);
                $dog2 = new Dog("มักกี้", 2, "Beagle");
                $cat1 = new Cat("มิมิ", 2, "ขาว", true);
                $cat2 = new Cat("ทิกเกอร์", 4, "ลายส้ม", false);

                $animals = [$dog1, $dog2, $cat1, $cat2];

                echo "<p><strong>ข้อมูลสัตว์:</strong></p>";
                echo "<table style='width: 100%; border-collapse: collapse;'>";
                echo "<tr style='background-color: #3498db; color: white;'>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>ชื่อ</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>ชนิด</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>อายุ</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>รายละเอียด</th>";
                echo "</tr>";
                
                foreach ($animals as $animal) {
                    $info = $animal->getInfo();
                    echo "<tr>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['name'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['species'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['age'] . " ปี</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>";
                    
                    if ($animal instanceof Dog) {
                        echo "สายพันธุ์: " . $info['breed'] . "<br>";
                        echo "ฝึกแล้ว: " . ($info['is_trained'] ? "ใช่" : "ไม่");
                    } elseif ($animal instanceof Cat) {
                        echo "สี: " . $info['color'] . "<br>";
                        echo "อยู่ในบ้าน: " . ($info['is_indoor'] ? "ใช่" : "ไม่");
                    }
                    
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                echo "<p><strong>พฤติกรรมของสัตว์:</strong></p>";
                echo "<ul>";
                foreach ($animals as $animal) {
                    echo "<li>" . $animal->makeSound() . "</li>";
                    echo "<li>" . $animal->eat() . "</li>";
                    
                    if ($animal instanceof Dog) {
                        echo "<li>" . $animal->fetch() . "</li>";
                        echo "<li>" . $animal->wagTail() . "</li>";
                    } elseif ($animal instanceof Cat) {
                        echo "<li>" . $animal->purr() . "</li>";
                        echo "<li>" . $animal->hunt() . "</li>";
                    }
                    echo "<br>";
                }
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="lab-section">
            <h2>5. Abstract Classes และ Interfaces</h2>
            <div class="output">
                <?php
                echo "<h3>5.1 Abstract Class</h3>";
                
                abstract class Shape {
                    protected $color;
                    protected $name;
                    
                    public function __construct($name, $color) {
                        $this->name = $name;
                        $this->color = $color;
                    }
                    
                    // Abstract method - ต้อง implement ใน subclass
                    abstract public function calculateArea();
                    abstract public function calculatePerimeter();
                    
                    // Concrete method
                    public function getInfo() {
                        return [
                            'name' => $this->name,
                            'color' => $this->color,
                            'area' => $this->calculateArea(),
                            'perimeter' => $this->calculatePerimeter()
                        ];
                    }
                    
                    public function paint($new_color) {
                        $old_color = $this->color;
                        $this->color = $new_color;
                        return "เปลี่ยนสี " . $this->name . " จาก $old_color เป็น $new_color";
                    }
                }

                class Rectangle extends Shape {
                    private $width;
                    private $height;
                    
                    public function __construct($width, $height, $color = "white") {
                        parent::__construct("Rectangle", $color);
                        $this->width = $width;
                        $this->height = $height;
                    }
                    
                    public function calculateArea() {
                        return $this->width * $this->height;
                    }
                    
                    public function calculatePerimeter() {
                        return 2 * ($this->width + $this->height);
                    }
                    
                    public function getDimensions() {
                        return ['width' => $this->width, 'height' => $this->height];
                    }
                }

                class Circle extends Shape {
                    private $radius;
                    
                    public function __construct($radius, $color = "white") {
                        parent::__construct("Circle", $color);
                        $this->radius = $radius;
                    }
                    
                    public function calculateArea() {
                        return pi() * pow($this->radius, 2);
                    }
                    
                    public function calculatePerimeter() {
                        return 2 * pi() * $this->radius;
                    }
                    
                    public function getRadius() {
                        return $this->radius;
                    }
                }

                echo "<h3>5.2 Interface</h3>";
                
                interface Drawable {
                    public function draw();
                    public function erase();
                }

                interface Resizable {
                    public function resize($scale_factor);
                }

                class Square extends Shape implements Drawable, Resizable {
                    private $side;
                    
                    public function __construct($side, $color = "white") {
                        parent::__construct("Square", $color);
                        $this->side = $side;
                    }
                    
                    public function calculateArea() {
                        return pow($this->side, 2);
                    }
                    
                    public function calculatePerimeter() {
                        return 4 * $this->side;
                    }
                    
                    public function draw() {
                        return "วาด " . $this->color . " square ขนาด " . $this->side . "x" . $this->side;
                    }
                    
                    public function erase() {
                        return "ลบ square ออก";
                    }
                    
                    public function resize($scale_factor) {
                        $old_side = $this->side;
                        $this->side *= $scale_factor;
                        return "ปรับขนาด square จาก {$old_side}x{$old_side} เป็น {$this->side}x{$this->side}";
                    }
                    
                    public function getSide() {
                        return $this->side;
                    }
                }

                // การใช้งาน
                $rectangle = new Rectangle(10, 5, "blue");
                $circle = new Circle(7, "red");
                $square = new Square(6, "green");

                $shapes = [$rectangle, $circle, $square];

                echo "<p><strong>ข้อมูลรูปทรง:</strong></p>";
                echo "<table style='width: 100%; border-collapse: collapse;'>";
                echo "<tr style='background-color: #3498db; color: white;'>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>รูปทรง</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>สี</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>พื้นที่</th>";
                echo "<th style='border: 1px solid #bdc3c7; padding: 8px;'>เส้นรอบรูป</th>";
                echo "</tr>";
                
                foreach ($shapes as $shape) {
                    $info = $shape->getInfo();
                    echo "<tr>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['name'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . $info['color'] . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . number_format($info['area'], 2) . "</td>";
                    echo "<td style='border: 1px solid #bdc3c7; padding: 8px;'>" . number_format($info['perimeter'], 2) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                echo "<p><strong>การทำงานกับ Interface:</strong></p>";
                echo "<ul>";
                echo "<li>" . $square->draw() . "</li>";
                echo "<li>" . $square->resize(1.5) . "</li>";
                echo "<li>พื้นที่ใหม่: " . number_format($square->calculateArea(), 2) . "</li>";
                echo "<li>" . $square->erase() . "</li>";
                echo "</ul>";

                echo "<p><strong>การเปลี่ยนสี:</strong></p>";
                echo "<ul>";
                echo "<li>" . $rectangle->paint("yellow") . "</li>";
                echo "<li>" . $circle->paint("purple") . "</li>";
                echo "</ul>";
                ?>
            </div>
        </div>

        <div class="info">
            <h3>สรุป OOP Concepts ที่เรียนรู้:</h3>
            <ul>
                <li><strong>Class และ Object:</strong> การสร้างแม่แบบและ instance</li>
                <li><strong>Properties และ Methods:</strong> ตัวแปรและฟังก์ชันของ class</li>
                <li><strong>Constructor:</strong> ฟังก์ชันที่ทำงานตอนสร้าง object</li>
                <li><strong>Visibility:</strong> public, private, protected</li>
                <li><strong>Static:</strong> Properties และ methods ที่เรียกใช้ผ่าน class</li>
                <li><strong>Inheritance:</strong> การสืบทอดคุณสมบัติจาก parent class</li>
                <li><strong>Abstract Class:</strong> Class ที่ไม่สามารถสร้าง instance ได้โดยตรง</li>
                <li><strong>Interface:</strong> การกำหนด contract ที่ class ต้อง implement</li>
            </ul>
        </div>
    </div>
</body>
</html>