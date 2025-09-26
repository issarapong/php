<?php
/**
 * REST API Router
 * ระบบจัดการ REST API Routes
 */

require_once 'advanced_helpers.php';
require_once '../lab02-database/database.php';

// กำหนด Headers
CORSHandler::setHeaders();

// กำหนด Content-Type
header('Content-Type: application/json; charset=utf-8');

// ดึงข้อมูล Request
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// ตัด base path ออกจาก request URI
if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// ตัด api.php ออก
$requestUri = str_replace('/api.php', '', $requestUri);
$requestUri = trim($requestUri, '/');

// แยก path segments
$segments = array_filter(explode('/', $requestUri));

// ดึงข้อมูล Input
$input = json_decode(file_get_contents('php://input'), true) ?? [];
if (empty($input)) {
    $input = $_POST;
}

// Query parameters
$queryParams = $_GET;

try {
    // เชื่อมต่อฐานข้อมูล
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Router
    if (empty($segments)) {
        // GET /api - API Information
        if ($method === 'GET') {
            APIResponse::success([
                'name' => 'Lab 05 Advanced API',
                'version' => '1.0.0',
                'endpoints' => [
                    'GET /api' => 'API information',
                    'POST /auth/login' => 'User login',
                    'POST /auth/register' => 'User registration',
                    'POST /auth/refresh' => 'Refresh token',
                    'GET /users' => 'Get all users (requires auth)',
                    'GET /users/{id}' => 'Get user by ID (requires auth)',
                    'PUT /users/{id}' => 'Update user (requires auth)',
                    'DELETE /users/{id}' => 'Delete user (requires auth)',
                    'GET /posts' => 'Get all posts',
                    'GET /posts/{id}' => 'Get post by ID',
                    'POST /posts' => 'Create post (requires auth)',
                    'PUT /posts/{id}' => 'Update post (requires auth)',
                    'DELETE /posts/{id}' => 'Delete post (requires auth)',
                    'POST /upload' => 'Upload file (requires auth)',
                    'GET /files' => 'List uploaded files (requires auth)'
                ]
            ], 'Lab 05 Advanced API');
        } else {
            APIResponse::error('Method not allowed', 405);
        }
    }
    
    // Authentication Routes
    elseif ($segments[0] === 'auth') {
        handleAuthRoutes($segments, $method, $input, $pdo);
    }
    
    // User Routes
    elseif ($segments[0] === 'users') {
        // ตรวจสอบ Authentication
        $user = authenticateRequest();
        handleUserRoutes($segments, $method, $input, $pdo, $user);
    }
    
    // Post Routes
    elseif ($segments[0] === 'posts') {
        handlePostRoutes($segments, $method, $input, $pdo);
    }
    
    // File Upload Routes
    elseif ($segments[0] === 'upload') {
        // ตรวจสอบ Authentication
        $user = authenticateRequest();
        handleUploadRoutes($segments, $method, $input, $pdo, $user);
    }
    
    // File List Routes
    elseif ($segments[0] === 'files') {
        // ตรวจสอบ Authentication
        $user = authenticateRequest();
        handleFileRoutes($segments, $method, $input, $pdo, $user);
    }
    
    else {
        APIResponse::notFound('Endpoint not found');
    }
    
} catch (Exception $e) {
    APIResponse::serverError($e->getMessage());
}

/**
 * ตรวจสอบ Authentication
 */
function authenticateRequest() {
    try {
        $payload = JWTHelper::validateFromHeader();
        return $payload;
    } catch (Exception $e) {
        APIResponse::unauthorized($e->getMessage());
    }
}

/**
 * จัดการ Authentication Routes
 */
function handleAuthRoutes($segments, $method, $input, $pdo) {
    if (count($segments) < 2) {
        APIResponse::notFound('Auth endpoint not found');
    }
    
    $action = $segments[1];
    
    switch ($action) {
        case 'register':
            if ($method === 'POST') {
                registerUser($input, $pdo);
            } else {
                APIResponse::error('Method not allowed', 405);
            }
            break;
            
        case 'login':
            if ($method === 'POST') {
                loginUser($input, $pdo);
            } else {
                APIResponse::error('Method not allowed', 405);
            }
            break;
            
        case 'refresh':
            if ($method === 'POST') {
                refreshToken($input, $pdo);
            } else {
                APIResponse::error('Method not allowed', 405);
            }
            break;
            
        default:
            APIResponse::notFound('Auth endpoint not found');
    }
}

/**
 * ลงทะเบียนผู้ใช้
 */
function registerUser($input, $pdo) {
    // Validate Input
    $validator = new InputValidator($input);
    $validator->required('username', 'Username is required')
             ->minLength('username', 3, 'Username must be at least 3 characters')
             ->required('email', 'Email is required')
             ->email('email', 'Invalid email format')
             ->required('password', 'Password is required')
             ->minLength('password', 6, 'Password must be at least 6 characters');
    
    if ($validator->fails()) {
        APIResponse::validationError($validator->getErrors());
    }
    
    // ตรวจสอบ Username และ Email ซ้ำ
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$input['username'], $input['email']]);
    
    if ($stmt->fetch()) {
        APIResponse::error('Username or email already exists', 409);
    }
    
    // สร้างผู้ใช้ใหม่
    $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$input['username'], $input['email'], $hashedPassword])) {
        $userId = $pdo->lastInsertId();
        
        // สร้าง JWT Token
        $payload = [
            'user_id' => $userId,
            'username' => $input['username'],
            'email' => $input['email']
        ];
        
        $token = JWTHelper::encode($payload);
        $refreshToken = JWTHelper::createRefreshToken($userId);
        
        // บันทึก refresh token
        $stmt = $pdo->prepare("UPDATE users SET refresh_token = ? WHERE id = ?");
        $stmt->execute([$refreshToken, $userId]);
        
        APIResponse::success([
            'user' => [
                'id' => $userId,
                'username' => $input['username'],
                'email' => $input['email']
            ],
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600
        ], 'User registered successfully', 201);
    } else {
        APIResponse::serverError('Failed to create user');
    }
}

/**
 * เข้าสู่ระบบ
 */
function loginUser($input, $pdo) {
    // Validate Input
    $validator = new InputValidator($input);
    $validator->required('username', 'Username or email is required')
             ->required('password', 'Password is required');
    
    if ($validator->fails()) {
        APIResponse::validationError($validator->getErrors());
    }
    
    // ค้นหาผู้ใช้
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$input['username'], $input['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($input['password'], $user['password'])) {
        APIResponse::unauthorized('Invalid credentials');
    }
    
    // อัพเดท last login
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // สร้าง JWT Token
    $payload = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email']
    ];
    
    $token = JWTHelper::encode($payload);
    $refreshToken = JWTHelper::createRefreshToken($user['id']);
    
    // บันทึก refresh token
    $stmt = $pdo->prepare("UPDATE users SET refresh_token = ? WHERE id = ?");
    $stmt->execute([$refreshToken, $user['id']]);
    
    APIResponse::success([
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'last_login' => $user['last_login']
        ],
        'access_token' => $token,
        'refresh_token' => $refreshToken,
        'token_type' => 'Bearer',
        'expires_in' => 3600
    ], 'Login successful');
}

/**
 * รีเฟรช Token
 */
function refreshToken($input, $pdo) {
    $validator = new InputValidator($input);
    $validator->required('refresh_token', 'Refresh token is required');
    
    if ($validator->fails()) {
        APIResponse::validationError($validator->getErrors());
    }
    
    // ตรวจสอบ refresh token
    $stmt = $pdo->prepare("SELECT * FROM users WHERE refresh_token = ?");
    $stmt->execute([$input['refresh_token']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        APIResponse::unauthorized('Invalid refresh token');
    }
    
    // สร้าง access token ใหม่
    $payload = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email']
    ];
    
    $newToken = JWTHelper::encode($payload);
    
    APIResponse::success([
        'access_token' => $newToken,
        'token_type' => 'Bearer',
        'expires_in' => 3600
    ], 'Token refreshed successfully');
}

/**
 * จัดการ User Routes
 */
function handleUserRoutes($segments, $method, $input, $pdo, $user) {
    if (count($segments) === 1) {
        // GET /users - ดูรายการผู้ใช้ทั้งหมด
        if ($method === 'GET') {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $offset = ($page - 1) * $limit;
            
            $stmt = $pdo->prepare("
                SELECT id, username, email, created_at, last_login 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // นับจำนวนทั้งหมด
            $countStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            APIResponse::success([
                'users' => $users,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => (int)$total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } else {
            APIResponse::error('Method not allowed', 405);
        }
    } elseif (count($segments) === 2) {
        $userId = $segments[1];
        
        switch ($method) {
            case 'GET':
                // GET /users/{id}
                $stmt = $pdo->prepare("
                    SELECT id, username, email, created_at, last_login 
                    FROM users WHERE id = ?
                ");
                $stmt->execute([$userId]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$userData) {
                    APIResponse::notFound('User not found');
                }
                
                APIResponse::success(['user' => $userData]);
                break;
                
            case 'PUT':
                // PUT /users/{id} - อัพเดทผู้ใช้
                if ($user['user_id'] != $userId) {
                    APIResponse::forbidden('You can only update your own profile');
                }
                
                $validator = new InputValidator($input);
                $validator->email('email', 'Invalid email format');
                
                if (isset($input['password'])) {
                    $validator->minLength('password', 6, 'Password must be at least 6 characters');
                }
                
                if ($validator->fails()) {
                    APIResponse::validationError($validator->getErrors());
                }
                
                $updateFields = [];
                $params = [];
                
                if (isset($input['email'])) {
                    $updateFields[] = "email = ?";
                    $params[] = $input['email'];
                }
                
                if (isset($input['password'])) {
                    $updateFields[] = "password = ?";
                    $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
                }
                
                if (empty($updateFields)) {
                    APIResponse::error('No fields to update');
                }
                
                $params[] = $userId;
                $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?");
                
                if ($stmt->execute($params)) {
                    APIResponse::success(null, 'User updated successfully');
                } else {
                    APIResponse::serverError('Failed to update user');
                }
                break;
                
            case 'DELETE':
                // DELETE /users/{id}
                if ($user['user_id'] != $userId) {
                    APIResponse::forbidden('You can only delete your own account');
                }
                
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    APIResponse::success(null, 'User deleted successfully');
                } else {
                    APIResponse::serverError('Failed to delete user');
                }
                break;
                
            default:
                APIResponse::error('Method not allowed', 405);
        }
    } else {
        APIResponse::notFound('User endpoint not found');
    }
}

/**
 * จัดการ Post Routes
 */
function handlePostRoutes($segments, $method, $input, $pdo) {
    if (count($segments) === 1) {
        switch ($method) {
            case 'GET':
                // GET /posts - ดูรายการโพสต์
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? 10;
                $category = $_GET['category'] ?? null;
                $offset = ($page - 1) * $limit;
                
                $where = "";
                $params = [];
                
                if ($category) {
                    $where = "WHERE c.name = ?";
                    $params[] = $category;
                }
                
                $params = array_merge($params, [$limit, $offset]);
                
                $stmt = $pdo->prepare("
                    SELECT p.*, u.username, c.name as category_name
                    FROM posts p
                    LEFT JOIN users u ON p.user_id = u.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    $where
                    ORDER BY p.created_at DESC
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute($params);
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // นับจำนวนทั้งหมด
                $countQuery = "SELECT COUNT(*) as total FROM posts p";
                if ($category) {
                    $countQuery .= " LEFT JOIN categories c ON p.category_id = c.id WHERE c.name = ?";
                    $countStmt = $pdo->prepare($countQuery);
                    $countStmt->execute([$category]);
                } else {
                    $countStmt = $pdo->query($countQuery);
                }
                $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                APIResponse::success([
                    'posts' => $posts,
                    'pagination' => [
                        'page' => (int)$page,
                        'limit' => (int)$limit,
                        'total' => (int)$total,
                        'total_pages' => ceil($total / $limit)
                    ]
                ]);
                break;
                
            case 'POST':
                // POST /posts - สร้างโพสต์ใหม่
                $user = authenticateRequest();
                
                $validator = new InputValidator($input);
                $validator->required('title', 'Title is required')
                         ->maxLength('title', 200, 'Title too long')
                         ->required('content', 'Content is required');
                
                if ($validator->fails()) {
                    APIResponse::validationError($validator->getErrors());
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO posts (user_id, title, content, category_id, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                
                $categoryId = $input['category_id'] ?? null;
                
                if ($stmt->execute([$user['user_id'], $input['title'], $input['content'], $categoryId])) {
                    $postId = $pdo->lastInsertId();
                    
                    // ดึงโพสต์ที่สร้างใหม่
                    $stmt = $pdo->prepare("
                        SELECT p.*, u.username, c.name as category_name
                        FROM posts p
                        LEFT JOIN users u ON p.user_id = u.id
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE p.id = ?
                    ");
                    $stmt->execute([$postId]);
                    $post = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    APIResponse::success(['post' => $post], 'Post created successfully', 201);
                } else {
                    APIResponse::serverError('Failed to create post');
                }
                break;
                
            default:
                APIResponse::error('Method not allowed', 405);
        }
    } elseif (count($segments) === 2) {
        $postId = $segments[1];
        
        switch ($method) {
            case 'GET':
                // GET /posts/{id}
                $stmt = $pdo->prepare("
                    SELECT p.*, u.username, c.name as category_name
                    FROM posts p
                    LEFT JOIN users u ON p.user_id = u.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.id = ?
                ");
                $stmt->execute([$postId]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$post) {
                    APIResponse::notFound('Post not found');
                }
                
                APIResponse::success(['post' => $post]);
                break;
                
            case 'PUT':
                // PUT /posts/{id}
                $user = authenticateRequest();
                
                // ตรวจสอบเจ้าของโพสต์
                $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
                $stmt->execute([$postId]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$post) {
                    APIResponse::notFound('Post not found');
                }
                
                if ($post['user_id'] != $user['user_id']) {
                    APIResponse::forbidden('You can only update your own posts');
                }
                
                $validator = new InputValidator($input);
                if (isset($input['title'])) {
                    $validator->maxLength('title', 200, 'Title too long');
                }
                
                if ($validator->fails()) {
                    APIResponse::validationError($validator->getErrors());
                }
                
                $updateFields = [];
                $params = [];
                
                if (isset($input['title'])) {
                    $updateFields[] = "title = ?";
                    $params[] = $input['title'];
                }
                
                if (isset($input['content'])) {
                    $updateFields[] = "content = ?";
                    $params[] = $input['content'];
                }
                
                if (isset($input['category_id'])) {
                    $updateFields[] = "category_id = ?";
                    $params[] = $input['category_id'];
                }
                
                if (empty($updateFields)) {
                    APIResponse::error('No fields to update');
                }
                
                $updateFields[] = "updated_at = NOW()";
                $params[] = $postId;
                
                $stmt = $pdo->prepare("UPDATE posts SET " . implode(', ', $updateFields) . " WHERE id = ?");
                
                if ($stmt->execute($params)) {
                    APIResponse::success(null, 'Post updated successfully');
                } else {
                    APIResponse::serverError('Failed to update post');
                }
                break;
                
            case 'DELETE':
                // DELETE /posts/{id}
                $user = authenticateRequest();
                
                // ตรวจสอบเจ้าของโพสต์
                $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
                $stmt->execute([$postId]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$post) {
                    APIResponse::notFound('Post not found');
                }
                
                if ($post['user_id'] != $user['user_id']) {
                    APIResponse::forbidden('You can only delete your own posts');
                }
                
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
                if ($stmt->execute([$postId])) {
                    APIResponse::success(null, 'Post deleted successfully');
                } else {
                    APIResponse::serverError('Failed to delete post');
                }
                break;
                
            default:
                APIResponse::error('Method not allowed', 405);
        }
    } else {
        APIResponse::notFound('Post endpoint not found');
    }
}

/**
 * จัดการ Upload Routes
 */
function handleUploadRoutes($segments, $method, $input, $pdo, $user) {
    if ($method === 'POST') {
        try {
            $uploadManager = new FileUploadManager('/workspaces/php/src/lab05-advanced/uploads/');
            
            if (isset($_FILES['file'])) {
                $result = $uploadManager->uploadSingle('file');
                
                // บันทึกข้อมูลไฟล์ในฐานข้อมูล
                $stmt = $pdo->prepare("
                    INSERT INTO uploaded_files (user_id, original_name, file_name, file_path, file_size, file_type, uploaded_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $stmt->execute([
                    $user['user_id'],
                    $result['originalName'],
                    $result['fileName'],
                    $result['filePath'],
                    $result['fileSize'],
                    $result['fileType']
                ]);
                
                $result['file_id'] = $pdo->lastInsertId();
                
                APIResponse::success($result, 'File uploaded successfully', 201);
            } elseif (isset($_FILES['files'])) {
                $results = $uploadManager->uploadMultiple('files');
                
                // บันทึกข้อมูลไฟล์ในฐานข้อมูล
                foreach ($results as &$result) {
                    if (isset($result['success']) && $result['success']) {
                        $stmt = $pdo->prepare("
                            INSERT INTO uploaded_files (user_id, original_name, file_name, file_path, file_size, file_type, uploaded_at)
                            VALUES (?, ?, ?, ?, ?, ?, NOW())
                        ");
                        
                        $stmt->execute([
                            $user['user_id'],
                            $result['originalName'],
                            $result['fileName'],
                            $result['filePath'],
                            $result['fileSize'],
                            $result['fileType']
                        ]);
                        
                        $result['file_id'] = $pdo->lastInsertId();
                    }
                }
                
                APIResponse::success($results, 'Files uploaded successfully', 201);
            } else {
                APIResponse::error('No files uploaded');
            }
            
        } catch (Exception $e) {
            APIResponse::error($e->getMessage());
        }
    } else {
        APIResponse::error('Method not allowed', 405);
    }
}

/**
 * จัดการ File Routes
 */
function handleFileRoutes($segments, $method, $input, $pdo, $user) {
    if ($method === 'GET') {
        // GET /files - รายการไฟล์ที่อัพโหลด
        $stmt = $pdo->prepare("
            SELECT f.*, u.username
            FROM uploaded_files f
            LEFT JOIN users u ON f.user_id = u.id
            WHERE f.user_id = ?
            ORDER BY f.uploaded_at DESC
        ");
        $stmt->execute([$user['user_id']]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        APIResponse::success(['files' => $files]);
    } else {
        APIResponse::error('Method not allowed', 405);
    }
}
?>