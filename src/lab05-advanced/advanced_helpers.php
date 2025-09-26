<?php
/**
 * JWT Helper Class
 * ระบบจัดการ JSON Web Token สำหรับ Authentication
 */
class JWTHelper {
    private static $secret = 'your-secret-key-2025';
    private static $algorithm = 'HS256';
    
    /**
     * สร้าง JWT Token
     */
    public static function encode($payload, $expiration = 3600) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);
        
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiration;
        $payload = json_encode($payload);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::$secret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }
    
    /**
     * ตรวจสอบและถอดรหัส JWT Token
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        
        if (count($parts) != 3) {
            throw new Exception('Invalid token format');
        }
        
        $header = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[0])), true);
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        $signature = str_replace(['-', '_'], ['+', '/'], $parts[2]);
        
        // ตรวจสอบ algorithm
        if ($header['alg'] !== self::$algorithm) {
            throw new Exception('Invalid algorithm');
        }
        
        // ตรวจสอบ signature
        $expectedSignature = hash_hmac('sha256', $parts[0] . "." . $parts[1], self::$secret, true);
        $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));
        
        if (!hash_equals($signature, $expectedSignature)) {
            throw new Exception('Invalid signature');
        }
        
        // ตรวจสอบ expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token expired');
        }
        
        return $payload;
    }
    
    /**
     * ตรวจสอบ Token จาก HTTP Header
     */
    public static function validateFromHeader() {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authHeader) {
            throw new Exception('Authorization header not found');
        }
        
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new Exception('Invalid authorization header format');
        }
        
        return self::decode($matches[1]);
    }
    
    /**
     * สร้าง Refresh Token
     */
    public static function createRefreshToken($userId) {
        return hash_hmac('sha256', $userId . time() . rand(), self::$secret);
    }
}

/**
 * File Upload Manager
 * ระบบจัดการการอัพโหลดไฟล์
 */
class FileUploadManager {
    private $uploadDir;
    private $allowedTypes;
    private $maxFileSize;
    private $allowedExtensions;
    
    public function __construct($uploadDir = 'uploads/', $maxFileSize = 5242880) { // 5MB default
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->maxFileSize = $maxFileSize;
        $this->allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'text/plain', 'application/json',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];
        $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'json', 'xlsx', 'xls'];
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * อัพโหลดไฟล์เดี่ยว
     */
    public function uploadSingle($fileInput, $customName = null) {
        if (!isset($_FILES[$fileInput])) {
            throw new Exception('No file uploaded');
        }
        
        $file = $_FILES[$fileInput];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Upload error: ' . $this->getUploadErrorMessage($file['error']));
        }
        
        return $this->processUpload($file, $customName);
    }
    
    /**
     * อัพโหลดไฟล์หลายไฟล์
     */
    public function uploadMultiple($fileInput) {
        if (!isset($_FILES[$fileInput])) {
            throw new Exception('No files uploaded');
        }
        
        $files = $_FILES[$fileInput];
        $results = [];
        
        // จัดการกรณีที่เป็น array
        if (is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                if ($file['error'] === UPLOAD_ERR_OK) {
                    try {
                        $results[] = $this->processUpload($file);
                    } catch (Exception $e) {
                        $results[] = ['error' => $e->getMessage(), 'file' => $file['name']];
                    }
                }
            }
        } else {
            $results[] = $this->processUpload($files);
        }
        
        return $results;
    }
    
    /**
     * ประมวลผลการอัพโหลด
     */
    private function processUpload($file, $customName = null) {
        // ตรวจสอบขนาดไฟล์
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('File too large. Max size: ' . $this->formatBytes($this->maxFileSize));
        }
        
        // ตรวจสอบประเภทไฟล์
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new Exception('File type not allowed: ' . $file['type']);
        }
        
        // ตรวจสอบนามสกุลไฟล์
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new Exception('File extension not allowed: ' . $extension);
        }
        
        // สร้างชื่อไฟล์ใหม่
        $fileName = $customName ? $customName . '.' . $extension : 
                   uniqid() . '_' . time() . '.' . $extension;
        
        $targetPath = $this->uploadDir . $fileName;
        
        // ตรวจสอบไฟล์ซ้ำ
        if (file_exists($targetPath)) {
            $fileName = uniqid() . '_' . $fileName;
            $targetPath = $this->uploadDir . $fileName;
        }
        
        // ย้ายไฟล์
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        // สร้าง thumbnail สำหรับรูปภาพ
        $thumbnail = null;
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $thumbnail = $this->createThumbnail($targetPath, $extension);
        }
        
        return [
            'success' => true,
            'fileName' => $fileName,
            'originalName' => $file['name'],
            'filePath' => $targetPath,
            'fileSize' => $file['size'],
            'fileType' => $file['type'],
            'extension' => $extension,
            'thumbnail' => $thumbnail,
            'url' => $this->getFileUrl($fileName),
            'uploadTime' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * สร้าง Thumbnail
     */
    private function createThumbnail($imagePath, $extension, $maxWidth = 200, $maxHeight = 200) {
        try {
            $thumbnailDir = $this->uploadDir . 'thumbnails/';
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            
            $fileName = basename($imagePath);
            $thumbnailPath = $thumbnailDir . 'thumb_' . $fileName;
            
            // ตรวจสอบ GD extension
            if (!extension_loaded('gd')) {
                return null;
            }
            
            // สร้างรูปภาพต้นฉบับ
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $sourceImage = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $sourceImage = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $sourceImage = imagecreatefromgif($imagePath);
                    break;
                case 'webp':
                    $sourceImage = imagecreatefromwebp($imagePath);
                    break;
                default:
                    return null;
            }
            
            if (!$sourceImage) return null;
            
            // คำนวณขนาดใหม่
            list($originalWidth, $originalHeight) = getimagesize($imagePath);
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            
            $newWidth = round($originalWidth * $ratio);
            $newHeight = round($originalHeight * $ratio);
            
            // สร้างรูปภาพใหม่
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            
            // รักษาความโปร่งใส
            if ($extension === 'png' || $extension === 'gif') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefill($thumbnail, 0, 0, $transparent);
            }
            
            // ปรับขนาดรูปภาพ
            imagecopyresampled(
                $thumbnail, $sourceImage, 0, 0, 0, 0,
                $newWidth, $newHeight, $originalWidth, $originalHeight
            );
            
            // บันทึกรูปภาพ
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($thumbnail, $thumbnailPath, 85);
                    break;
                case 'png':
                    imagepng($thumbnail, $thumbnailPath);
                    break;
                case 'gif':
                    imagegif($thumbnail, $thumbnailPath);
                    break;
                case 'webp':
                    imagewebp($thumbnail, $thumbnailPath);
                    break;
            }
            
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);
            
            return [
                'path' => $thumbnailPath,
                'url' => $this->getFileUrl('thumbnails/thumb_' . $fileName),
                'width' => $newWidth,
                'height' => $newHeight
            ];
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * ลบไฟล์
     */
    public function deleteFile($fileName) {
        $filePath = $this->uploadDir . $fileName;
        $thumbnailPath = $this->uploadDir . 'thumbnails/thumb_' . $fileName;
        
        $result = ['file_deleted' => false, 'thumbnail_deleted' => false];
        
        if (file_exists($filePath)) {
            $result['file_deleted'] = unlink($filePath);
        }
        
        if (file_exists($thumbnailPath)) {
            $result['thumbnail_deleted'] = unlink($thumbnailPath);
        }
        
        return $result;
    }
    
    /**
     * รับ URL ของไฟล์
     */
    private function getFileUrl($fileName) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        return $protocol . $host . $scriptDir . '/' . $this->uploadDir . $fileName;
    }
    
    /**
     * ข้อความ Error
     */
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File too large (php.ini limit)';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File too large (form limit)';
            case UPLOAD_ERR_PARTIAL:
                return 'File partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'No temporary directory';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Cannot write to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload blocked by extension';
            default:
                return 'Unknown error';
        }
    }
    
    /**
     * แปลงขนาดไฟล์
     */
    private function formatBytes($size) {
        $units = array('B', 'KB', 'MB', 'GB');
        for ($i = 0; $size >= 1024 && $i < 3; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
    
    /**
     * ดูรายการไฟล์ที่อัพโหลดแล้ว
     */
    public function listUploads() {
        $files = [];
        $directory = new DirectoryIterator($this->uploadDir);
        
        foreach ($directory as $fileInfo) {
            if (!$fileInfo->isDot() && !$fileInfo->isDir()) {
                $fileName = $fileInfo->getFilename();
                if ($fileName !== '.htaccess') { // ข้าม .htaccess
                    $files[] = [
                        'name' => $fileName,
                        'size' => $fileInfo->getSize(),
                        'modified' => date('Y-m-d H:i:s', $fileInfo->getMTime()),
                        'url' => $this->getFileUrl($fileName),
                        'type' => mime_content_type($fileInfo->getPathname())
                    ];
                }
            }
        }
        
        return $files;
    }
}

/**
 * API Response Helper
 * ระบบจัดการ JSON Response
 */
class APIResponse {
    /**
     * ส่ง Success Response
     */
    public static function success($data = null, $message = 'Success', $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => true,
            'message' => $message,
            'code' => $code,
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $data
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * ส่ง Error Response
     */
    public static function error($message = 'Error', $code = 400, $errors = null) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * ส่ง Validation Error Response
     */
    public static function validationError($errors, $message = 'Validation failed') {
        self::error($message, 422, $errors);
    }
    
    /**
     * ส่ง Unauthorized Response
     */
    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }
    
    /**
     * ส่ง Not Found Response
     */
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    /**
     * ส่ง Forbidden Response
     */
    public static function forbidden($message = 'Forbidden') {
        self::error($message, 403);
    }
    
    /**
     * ส่ง Server Error Response
     */
    public static function serverError($message = 'Internal server error') {
        self::error($message, 500);
    }
}

/**
 * Input Validator
 * ระบบตรวจสอบข้อมูล Input
 */
class InputValidator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * ตรวจสอบฟิลด์ที่จำเป็น
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field][] = $message ?? "$field is required";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบอีเมล
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = $message ?? "$field must be a valid email";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบความยาวขั้นต่ำ
     */
    public function minLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field][] = $message ?? "$field must be at least $length characters";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบความยาวสูงสุด
     */
    public function maxLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field][] = $message ?? "$field must not exceed $length characters";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบตัวเลข
     */
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = $message ?? "$field must be numeric";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบค่าขั้นต่ำ
     */
    public function min($field, $min, $message = null) {
        if (isset($this->data[$field]) && $this->data[$field] < $min) {
            $this->errors[$field][] = $message ?? "$field must be at least $min";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบค่าสูงสุด
     */
    public function max($field, $max, $message = null) {
        if (isset($this->data[$field]) && $this->data[$field] > $max) {
            $this->errors[$field][] = $message ?? "$field must not exceed $max";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบรูปแบบ (RegEx)
     */
    public function pattern($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->errors[$field][] = $message ?? "$field format is invalid";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบว่าอยู่ในรายการที่กำหนด
     */
    public function in($field, $values, $message = null) {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[$field][] = $message ?? "$field must be one of: " . implode(', ', $values);
        }
        return $this;
    }
    
    /**
     * ตรวจสอบ URL
     */
    public function url($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[$field][] = $message ?? "$field must be a valid URL";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบวันที่
     */
    public function date($field, $format = 'Y-m-d', $message = null) {
        if (isset($this->data[$field])) {
            $date = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$date || $date->format($format) !== $this->data[$field]) {
                $this->errors[$field][] = $message ?? "$field must be a valid date in format $format";
            }
        }
        return $this;
    }
    
    /**
     * เพิ่ม Custom Rule
     */
    public function custom($field, $callback, $message = null) {
        if (isset($this->data[$field]) && !$callback($this->data[$field])) {
            $this->errors[$field][] = $message ?? "$field is invalid";
        }
        return $this;
    }
    
    /**
     * ตรวจสอบว่าผ่านการ Validate หรือไม่
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * ตรวจสอบว่าไม่ผ่านการ Validate
     */
    public function fails() {
        return !$this->passes();
    }
    
    /**
     * ดึง Errors ทั้งหมด
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * ดึงข้อมูลที่ผ่านการตรวจสอบ
     */
    public function getValidData() {
        $validData = [];
        foreach ($this->data as $key => $value) {
            if (!isset($this->errors[$key])) {
                $validData[$key] = $value;
            }
        }
        return $validData;
    }
}

/**
 * CORS Handler
 * จัดการ Cross-Origin Resource Sharing
 */
class CORSHandler {
    /**
     * ตั้งค่า CORS Headers
     */
    public static function setHeaders($allowedOrigins = ['*'], $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], $allowedHeaders = ['*']) {
        // ตรวจสอบ Origin
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . ($origin ?: '*'));
        }
        
        header("Access-Control-Allow-Methods: " . implode(', ', $allowedMethods));
        header("Access-Control-Allow-Headers: " . (in_array('*', $allowedHeaders) ? 
            'Origin, X-Requested-With, Content-Type, Accept, Authorization' : 
            implode(', ', $allowedHeaders)));
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400");
        
        // จัดการ Preflight Request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
}
?>