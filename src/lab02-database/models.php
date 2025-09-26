<?php
require_once 'database.php';

class User extends BaseModel {
    protected $table = 'users';
    
    public function create($data) {
        // ตรวจสอบข้อมูล
        $validation = $this->validateUserData($data);
        if (!$validation['valid']) {
            return ErrorHandler::handle($validation['message']);
        }
        
        // เข้ารหัสรหัสผ่าน
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO {$this->table} (username, email, password, full_name) VALUES (?, ?, ?, ?)";
        
        try {
            $stmt = $this->execute($sql, [
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['full_name']
            ]);
            
            $userId = $this->db->lastInsertId();
            return ErrorHandler::success('สร้างผู้ใช้สำเร็จ', [
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name']
            ]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'username') !== false) {
                return ErrorHandler::handle('ชื่อผู้ใช้นี้มีอยู่แล้ว');
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                return ErrorHandler::handle('อีเมลนี้มีอยู่แล้ว');
            }
            return ErrorHandler::handle('เกิดข้อผิดพลาดในการสร้างผู้ใช้: ' . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        // ตรวจสอบว่าผู้ใช้มีอยู่จริง
        if (!$this->findById($id)) {
            return ErrorHandler::handle('ไม่พบผู้ใช้ที่ต้องการอัพเดต');
        }
        
        $updateFields = [];
        $params = [];
        
        if (isset($data['username']) && !empty($data['username'])) {
            $updateFields[] = "username = ?";
            $params[] = $data['username'];
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ErrorHandler::handle('รูปแบบอีเมลไม่ถูกต้อง');
            }
            $updateFields[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['full_name']) && !empty($data['full_name'])) {
            $updateFields[] = "full_name = ?";
            $params[] = $data['full_name'];
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $updateFields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($updateFields)) {
            return ErrorHandler::handle('ไม่มีข้อมูลที่ต้องการอัพเดต');
        }
        
        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        try {
            $stmt = $this->execute($sql, $params);
            if ($stmt->rowCount() > 0) {
                $updatedUser = $this->findById($id);
                unset($updatedUser['password']); // ไม่ส่งรหัสผ่านกลับ
                return ErrorHandler::success('อัพเดตผู้ใช้สำเร็จ', $updatedUser);
            } else {
                return ErrorHandler::handle('ไม่มีการเปลี่ยนแปลงข้อมูล');
            }
        } catch (Exception $e) {
            return ErrorHandler::handle('เกิดข้อผิดพลาดในการอัพเดต: ' . $e->getMessage());
        }
    }
    
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? OR email = ?";
        
        try {
            $stmt = $this->execute($sql, [$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']); // ไม่ส่งรหัสผ่านกลับ
                return ErrorHandler::success('เข้าสู่ระบบสำเร็จ', $user);
            } else {
                return ErrorHandler::handle('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            }
        } catch (Exception $e) {
            return ErrorHandler::handle('เกิดข้อผิดพลาดในการเข้าสู่ระบบ: ' . $e->getMessage());
        }
    }
    
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->execute($sql, [$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            unset($user['password']);
        }
        
        return $user;
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->execute($sql, [$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            unset($user['password']);
        }
        
        return $user;
    }
    
    public function getAllWithoutPassword() {
        $sql = "SELECT id, username, email, full_name, created_at, updated_at FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->execute($sql);
        return $stmt->fetchAll();
    }
    
    public function searchUsers($searchTerm) {
        $sql = "SELECT id, username, email, full_name, created_at, updated_at 
                FROM {$this->table} 
                WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ?
                ORDER BY created_at DESC";
        
        $searchParam = "%{$searchTerm}%";
        $stmt = $this->execute($sql, [$searchParam, $searchParam, $searchParam]);
        return $stmt->fetchAll();
    }
    
    private function validateUserData($data) {
        $errors = [];
        
        // ตรวจสอบ username
        if (empty($data['username'])) {
            $errors[] = 'กรุณาระบุชื่อผู้ใช้';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร';
        } elseif (strlen($data['username']) > 50) {
            $errors[] = 'ชื่อผู้ใช้ต้องไม่เกิน 50 ตัวอักษร';
        }
        
        // ตรวจสอบ email
        if (empty($data['email'])) {
            $errors[] = 'กรุณาระบุอีเมล';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
        }
        
        // ตรวจสอบ password
        if (empty($data['password'])) {
            $errors[] = 'กรุณาระบุรหัสผ่าน';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        }
        
        // ตรวจสอบ full_name
        if (empty($data['full_name'])) {
            $errors[] = 'กรุณาระบุชื่อเต็ม';
        } elseif (strlen($data['full_name']) > 100) {
            $errors[] = 'ชื่อเต็มต้องไม่เกิน 100 ตัวอักษร';
        }
        
        return [
            'valid' => empty($errors),
            'message' => implode(', ', $errors)
        ];
    }
}

class Post extends BaseModel {
    protected $table = 'posts';
    
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (user_id, title, content, status) VALUES (?, ?, ?, ?)";
        
        try {
            $stmt = $this->execute($sql, [
                $data['user_id'],
                $data['title'],
                $data['content'],
                $data['status'] ?? 'draft'
            ]);
            
            $postId = $this->db->lastInsertId();
            return ErrorHandler::success('สร้างโพสต์สำเร็จ', $this->findById($postId));
        } catch (Exception $e) {
            return ErrorHandler::handle('เกิดข้อผิดพลาดในการสร้างโพสต์: ' . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        if (!$this->findById($id)) {
            return ErrorHandler::handle('ไม่พบโพสต์ที่ต้องการอัพเดต');
        }
        
        $updateFields = [];
        $params = [];
        
        if (isset($data['title'])) {
            $updateFields[] = "title = ?";
            $params[] = $data['title'];
        }
        
        if (isset($data['content'])) {
            $updateFields[] = "content = ?";
            $params[] = $data['content'];
        }
        
        if (isset($data['status'])) {
            $updateFields[] = "status = ?";
            $params[] = $data['status'];
        }
        
        if (empty($updateFields)) {
            return ErrorHandler::handle('ไม่มีข้อมูลที่ต้องการอัพเดต');
        }
        
        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        try {
            $stmt = $this->execute($sql, $params);
            return ErrorHandler::success('อัพเดตโพสต์สำเร็จ', $this->findById($id));
        } catch (Exception $e) {
            return ErrorHandler::handle('เกิดข้อผิดพลาดในการอัพเดต: ' . $e->getMessage());
        }
    }
    
    public function getPostsWithUsers() {
        $sql = "SELECT p.*, u.username, u.full_name 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC";
        $stmt = $this->execute($sql);
        return $stmt->fetchAll();
    }
    
    public function getPostsByUser($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->execute($sql, [$userId]);
        return $stmt->fetchAll();
    }
    
    public function getPublishedPosts() {
        $sql = "SELECT p.*, u.username, u.full_name 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'published' 
                ORDER BY p.created_at DESC";
        $stmt = $this->execute($sql);
        return $stmt->fetchAll();
    }
}

class Category extends BaseModel {
    protected $table = 'categories';
    
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, description) VALUES (?, ?)";
        
        try {
            $stmt = $this->execute($sql, [
                $data['name'],
                $data['description'] ?? null
            ]);
            
            $categoryId = $this->db->lastInsertId();
            return ErrorHandler::success('สร้างหมวดหมู่สำเร็จ', $this->findById($categoryId));
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'name') !== false) {
                return ErrorHandler::handle('ชื่อหมวดหมู่นี้มีอยู่แล้ว');
            }
            return ErrorHandler::handle('เกิดข้อผิดพลาดในการสร้างหมวดหมู่: ' . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        if (!$this->findById($id)) {
            return ErrorHandler::handle('ไม่พบหมวดหมู่ที่ต้องการอัพเดต');
        }
        
        $sql = "UPDATE {$this->table} SET name = ?, description = ? WHERE id = ?";
        
        try {
            $stmt = $this->execute($sql, [
                $data['name'],
                $data['description'] ?? null,
                $id
            ]);
            
            return ErrorHandler::success('อัพเดตหมวดหมู่สำเร็จ', $this->findById($id));
        } catch (Exception $e) {
            return ErrorHandler::handle('เกิดข้อผิดพลาดในการอัพเดต: ' . $e->getMessage());
        }
    }
    
    public function getCategoriesWithPostCount() {
        $sql = "SELECT c.*, 
                       COUNT(pc.post_id) as post_count 
                FROM {$this->table} c 
                LEFT JOIN post_categories pc ON c.id = pc.category_id 
                GROUP BY c.id 
                ORDER BY c.name";
        $stmt = $this->execute($sql);
        return $stmt->fetchAll();
    }
}
?>