<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 05: Advanced Techniques</title>
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .lab-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .lab-title { color: #2c3e50; border-bottom: 3px solid #e74c3c; padding-bottom: 10px; }
        .output { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; max-height: 400px; overflow-y: auto; }
        .success { background-color: #d5f4e6; border-left: 4px solid #27ae60; padding: 15px; margin: 10px 0; }
        .error { background-color: #fadbd8; border-left: 4px solid #e74c3c; padding: 15px; margin: 10px 0; }
        .info { background-color: #d5dbdb; border-left: 4px solid #3498db; padding: 15px; margin: 10px 0; }
        .warning { background-color: #fcf3cf; border-left: 4px solid #f1c40f; padding: 15px; margin: 10px 0; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #2c3e50; }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; padding: 8px; border: 1px solid #bdc3c7; border-radius: 4px; box-sizing: border-box;
        }
        .btn { display: inline-block; padding: 10px 20px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px; margin: 5px; cursor: pointer; border: none; }
        .btn:hover { background: #c0392b; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #229954; }
        .btn-info { background: #3498db; }
        .btn-info:hover { background: #2980b9; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .api-test { border: 2px solid #3498db; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .response { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; margin: 10px 0; }
        .tab-container { margin: 20px 0; }
        .tabs { display: flex; background: #ecf0f1; border-radius: 5px 5px 0 0; }
        .tab { padding: 12px 20px; cursor: pointer; border-radius: 5px 5px 0 0; margin-right: 2px; }
        .tab.active { background: #3498db; color: white; }
        .tab-content { display: none; padding: 20px; background: white; border-radius: 0 0 5px 5px; }
        .tab-content.active { display: block; }
        .file-upload { border: 2px dashed #bdc3c7; padding: 20px; text-align: center; border-radius: 5px; margin: 10px 0; }
        .file-upload:hover { border-color: #3498db; }
        .code-block { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto; }
        .badge { background: #e74c3c; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; margin-left: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #bdc3c7; padding: 8px; text-align: left; }
        th { background-color: #e74c3c; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="lab-title">Lab 05: Advanced Techniques - REST API, JWT, File Upload</h1>

        <!-- JWT Authentication Section -->
        <div class="lab-section">
            <h2>1. JWT Authentication System</h2>
            
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active" onclick="showTab('register-tab')">Register</div>
                    <div class="tab" onclick="showTab('login-tab')">Login</div>
                    <div class="tab" onclick="showTab('token-test-tab')">Test Token</div>
                </div>
                
                <div id="register-tab" class="tab-content active">
                    <h3>User Registration</h3>
                    <div class="api-test">
                        <form id="registerForm">
                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" id="reg_username" name="username" placeholder="Enter username" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" id="reg_email" name="email" placeholder="Enter email" required>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" id="reg_password" name="password" placeholder="Enter password" required>
                            </div>
                            <button type="submit" class="btn btn-success">Register</button>
                        </form>
                    </div>
                    <div id="register-response" class="response" style="display: none;"></div>
                </div>
                
                <div id="login-tab" class="tab-content">
                    <h3>User Login</h3>
                    <div class="api-test">
                        <form id="loginForm">
                            <div class="form-group">
                                <label>Username or Email:</label>
                                <input type="text" id="login_username" name="username" placeholder="Enter username or email" required>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" id="login_password" name="password" placeholder="Enter password" required>
                            </div>
                            <button type="submit" class="btn btn-info">Login</button>
                        </form>
                    </div>
                    <div id="login-response" class="response" style="display: none;"></div>
                </div>
                
                <div id="token-test-tab" class="tab-content">
                    <h3>Test JWT Token</h3>
                    <div class="api-test">
                        <div class="form-group">
                            <label>Access Token:</label>
                            <input type="text" id="test_token" placeholder="Paste your access token here">
                            <small>ใช้ token จาก Login หรือ Register</small>
                        </div>
                        <button onclick="testToken()" class="btn btn-warning">Test Token</button>
                        <button onclick="getProfile()" class="btn btn-info">Get Profile</button>
                    </div>
                    <div id="token-response" class="response" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- REST API Section -->
        <div class="lab-section">
            <h2>2. REST API Operations</h2>
            
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active" onclick="showTab('posts-list-tab')">Posts List</div>
                    <div class="tab" onclick="showTab('create-post-tab')">Create Post</div>
                    <div class="tab" onclick="showTab('api-docs-tab')">API Docs</div>
                </div>
                
                <div id="posts-list-tab" class="tab-content active">
                    <h3>Get Posts</h3>
                    <div class="api-test">
                        <div class="form-group">
                            <label>Page:</label>
                            <input type="number" id="posts_page" value="1" min="1">
                        </div>
                        <div class="form-group">
                            <label>Limit:</label>
                            <input type="number" id="posts_limit" value="5" min="1" max="50">
                        </div>
                        <div class="form-group">
                            <label>Category:</label>
                            <select id="posts_category">
                                <option value="">All Categories</option>
                                <option value="Technology">Technology</option>
                                <option value="Lifestyle">Lifestyle</option>
                                <option value="Education">Education</option>
                            </select>
                        </div>
                        <button onclick="getPosts()" class="btn btn-info">Get Posts</button>
                    </div>
                    <div id="posts-response" class="response" style="display: none;"></div>
                </div>
                
                <div id="create-post-tab" class="tab-content">
                    <h3>Create New Post <span class="badge">Requires Auth</span></h3>
                    <div class="api-test">
                        <form id="createPostForm">
                            <div class="form-group">
                                <label>Access Token:</label>
                                <input type="text" id="post_token" placeholder="Enter access token" required>
                            </div>
                            <div class="form-group">
                                <label>Title:</label>
                                <input type="text" id="post_title" placeholder="Enter post title" required>
                            </div>
                            <div class="form-group">
                                <label>Content:</label>
                                <textarea id="post_content" rows="5" placeholder="Enter post content" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Category:</label>
                                <select id="post_category">
                                    <option value="">Select Category</option>
                                    <option value="1">Technology</option>
                                    <option value="2">Lifestyle</option>
                                    <option value="3">Education</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Create Post</button>
                        </form>
                    </div>
                    <div id="create-post-response" class="response" style="display: none;"></div>
                </div>
                
                <div id="api-docs-tab" class="tab-content">
                    <h3>API Documentation</h3>
                    <div class="info">
                        <h4>📚 Available Endpoints:</h4>
                        <table>
                            <tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Auth Required</th></tr>
                            <tr><td>GET</td><td>/api</td><td>API Information</td><td>No</td></tr>
                            <tr><td>POST</td><td>/auth/register</td><td>User Registration</td><td>No</td></tr>
                            <tr><td>POST</td><td>/auth/login</td><td>User Login</td><td>No</td></tr>
                            <tr><td>POST</td><td>/auth/refresh</td><td>Refresh Token</td><td>No</td></tr>
                            <tr><td>GET</td><td>/users</td><td>Get All Users</td><td>Yes</td></tr>
                            <tr><td>GET</td><td>/users/{id}</td><td>Get User by ID</td><td>Yes</td></tr>
                            <tr><td>PUT</td><td>/users/{id}</td><td>Update User</td><td>Yes</td></tr>
                            <tr><td>DELETE</td><td>/users/{id}</td><td>Delete User</td><td>Yes</td></tr>
                            <tr><td>GET</td><td>/posts</td><td>Get All Posts</td><td>No</td></tr>
                            <tr><td>GET</td><td>/posts/{id}</td><td>Get Post by ID</td><td>No</td></tr>
                            <tr><td>POST</td><td>/posts</td><td>Create Post</td><td>Yes</td></tr>
                            <tr><td>PUT</td><td>/posts/{id}</td><td>Update Post</td><td>Yes</td></tr>
                            <tr><td>DELETE</td><td>/posts/{id}</td><td>Delete Post</td><td>Yes</td></tr>
                            <tr><td>POST</td><td>/upload</td><td>Upload File</td><td>Yes</td></tr>
                            <tr><td>GET</td><td>/files</td><td>List Files</td><td>Yes</td></tr>
                        </table>
                    </div>
                    
                    <button onclick="getAPIInfo()" class="btn btn-info">Get API Info</button>
                    <div id="api-info-response" class="response" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- File Upload Section -->
        <div class="lab-section">
            <h2>3. File Upload System</h2>
            
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active" onclick="showTab('single-upload-tab')">Single Upload</div>
                    <div class="tab" onclick="showTab('multiple-upload-tab')">Multiple Upload</div>
                    <div class="tab" onclick="showTab('file-list-tab')">My Files</div>
                </div>
                
                <div id="single-upload-tab" class="tab-content active">
                    <h3>Single File Upload <span class="badge">Requires Auth</span></h3>
                    <div class="api-test">
                        <form id="singleUploadForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Access Token:</label>
                                <input type="text" id="upload_token" placeholder="Enter access token" required>
                            </div>
                            <div class="form-group">
                                <label>Select File:</label>
                                <div class="file-upload" onclick="document.getElementById('single-file').click();">
                                    <p>Click to select file or drag and drop</p>
                                    <input type="file" id="single-file" name="file" style="display: none;" onchange="updateFileName('single-file', 'single-filename')">
                                    <div id="single-filename" style="margin-top: 10px; font-weight: bold;"></div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Upload File</button>
                        </form>
                    </div>
                    <div id="single-upload-response" class="response" style="display: none;"></div>
                </div>
                
                <div id="multiple-upload-tab" class="tab-content">
                    <h3>Multiple Files Upload <span class="badge">Requires Auth</span></h3>
                    <div class="api-test">
                        <form id="multipleUploadForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Access Token:</label>
                                <input type="text" id="multi_upload_token" placeholder="Enter access token" required>
                            </div>
                            <div class="form-group">
                                <label>Select Files:</label>
                                <div class="file-upload" onclick="document.getElementById('multiple-files').click();">
                                    <p>Click to select multiple files</p>
                                    <input type="file" id="multiple-files" name="files[]" multiple style="display: none;" onchange="updateFileNames('multiple-files', 'multiple-filenames')">
                                    <div id="multiple-filenames" style="margin-top: 10px; font-weight: bold;"></div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Upload Files</button>
                        </form>
                    </div>
                    <div id="multiple-upload-response" class="response" style="display: none;"></div>
                </div>
                
                <div id="file-list-tab" class="tab-content">
                    <h3>My Uploaded Files <span class="badge">Requires Auth</span></h3>
                    <div class="api-test">
                        <div class="form-group">
                            <label>Access Token:</label>
                            <input type="text" id="files_token" placeholder="Enter access token" required>
                        </div>
                        <button onclick="getMyFiles()" class="btn btn-info">Get My Files</button>
                    </div>
                    <div id="files-response" class="response" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- JSON Processing Section -->
        <div class="lab-section">
            <h2>4. JSON Data Processing</h2>
            <div class="output">
                <?php
                echo "<h3>4.1 JSON Examples และการประมวลผล</h3>";
                
                // ตัวอย่าง JSON data
                $sampleData = [
                    'user' => [
                        'id' => 1,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'profile' => [
                            'age' => 30,
                            'city' => 'Bangkok',
                            'interests' => ['programming', 'music', 'travel']
                        ]
                    ],
                    'posts' => [
                        [
                            'id' => 1,
                            'title' => 'Learning PHP',
                            'content' => 'PHP is a great programming language...',
                            'tags' => ['php', 'programming', 'web']
                        ],
                        [
                            'id' => 2,
                            'title' => 'Working with JSON',
                            'content' => 'JSON is easy to work with...',
                            'tags' => ['json', 'data', 'api']
                        ]
                    ],
                    'metadata' => [
                        'created_at' => date('Y-m-d H:i:s'),
                        'version' => '1.0',
                        'total_posts' => 2
                    ]
                ];
                
                echo "<div class='info'>";
                echo "<h4>📄 Sample JSON Data:</h4>";
                echo "<div class='code-block'>";
                echo json_encode($sampleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                echo "</div>";
                echo "</div>";
                
                echo "<h4>JSON Operations:</h4>";
                
                // JSON Encoding
                $jsonString = json_encode($sampleData, JSON_UNESCAPED_UNICODE);
                echo "<div class='success'>";
                echo "<h5>✅ JSON Encode:</h5>";
                echo "<p><strong>Size:</strong> " . strlen($jsonString) . " bytes</p>";
                echo "<p><strong>Pretty Print:</strong> " . (json_encode($sampleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ? 'Available' : 'Not Available') . "</p>";
                echo "</div>";
                
                // JSON Decoding
                $decodedData = json_decode($jsonString, true);
                echo "<div class='info'>";
                echo "<h5>🔍 JSON Decode Results:</h5>";
                echo "<p><strong>User Name:</strong> " . $decodedData['user']['name'] . "</p>";
                echo "<p><strong>User City:</strong> " . $decodedData['user']['profile']['city'] . "</p>";
                echo "<p><strong>Total Posts:</strong> " . count($decodedData['posts']) . "</p>";
                echo "<p><strong>First Post Tags:</strong> " . implode(', ', $decodedData['posts'][0]['tags']) . "</p>";
                echo "</div>";
                
                // JSON Validation
                echo "<h4>JSON Validation Examples:</h4>";
                
                $validJson = '{"name": "John", "age": 30}';
                $invalidJson = '{"name": "John", "age": 30'; // Missing closing brace
                
                echo "<div class='info'>";
                echo "<h5>Valid JSON Test:</h5>";
                echo "<p><strong>Input:</strong> <code>$validJson</code></p>";
                $result = json_decode($validJson);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "<p class='success'>✅ Valid JSON</p>";
                } else {
                    echo "<p class='error'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
                }
                echo "</div>";
                
                echo "<div class='warning'>";
                echo "<h5>Invalid JSON Test:</h5>";
                echo "<p><strong>Input:</strong> <code>$invalidJson</code></p>";
                $result = json_decode($invalidJson);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "<p class='success'>✅ Valid JSON</p>";
                } else {
                    echo "<p class='error'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
                }
                echo "</div>";
                
                // JSON Manipulation
                echo "<h4>JSON Data Manipulation:</h4>";
                
                $manipulatedData = $sampleData;
                
                // เพิ่มข้อมูล
                $manipulatedData['user']['profile']['phone'] = '081-234-5678';
                $manipulatedData['user']['profile']['interests'][] = 'cooking';
                
                // อัพเดทข้อมูล
                $manipulatedData['metadata']['updated_at'] = date('Y-m-d H:i:s');
                $manipulatedData['metadata']['total_posts'] = count($manipulatedData['posts']);
                
                // เพิ่มโพสต์ใหม่
                $manipulatedData['posts'][] = [
                    'id' => 3,
                    'title' => 'Advanced PHP Techniques',
                    'content' => 'In this post, we will explore...',
                    'tags' => ['php', 'advanced', 'techniques']
                ];
                
                // อัพเดทจำนวนโพสต์
                $manipulatedData['metadata']['total_posts'] = count($manipulatedData['posts']);
                
                echo "<div class='success'>";
                echo "<h5>📝 After Manipulation:</h5>";
                echo "<p><strong>Added Phone:</strong> " . $manipulatedData['user']['profile']['phone'] . "</p>";
                echo "<p><strong>Total Interests:</strong> " . count($manipulatedData['user']['profile']['interests']) . "</p>";
                echo "<p><strong>Total Posts:</strong> " . $manipulatedData['metadata']['total_posts'] . "</p>";
                echo "<p><strong>New Post Title:</strong> " . end($manipulatedData['posts'])['title'] . "</p>";
                echo "</div>";
                
                // JSON Schema Validation Example
                echo "<h4>JSON Schema Validation Example:</h4>";
                
                function validateUserSchema($data) {
                    $errors = [];
                    
                    // ตรวจสอบฟิลด์ที่จำเป็น
                    if (!isset($data['name']) || empty($data['name'])) {
                        $errors[] = 'Name is required';
                    }
                    
                    if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'Valid email is required';
                    }
                    
                    if (!isset($data['age']) || !is_numeric($data['age']) || $data['age'] < 0) {
                        $errors[] = 'Valid age is required';
                    }
                    
                    return $errors;
                }
                
                $testUser1 = ['name' => 'John Doe', 'email' => 'john@example.com', 'age' => 30];
                $testUser2 = ['name' => '', 'email' => 'invalid-email', 'age' => -5];
                
                echo "<div class='info'>";
                echo "<h5>Valid User Data:</h5>";
                echo "<p><strong>Data:</strong> " . json_encode($testUser1) . "</p>";
                $errors1 = validateUserSchema($testUser1);
                if (empty($errors1)) {
                    echo "<p class='success'>✅ Schema validation passed</p>";
                } else {
                    echo "<p class='error'>❌ Errors: " . implode(', ', $errors1) . "</p>";
                }
                echo "</div>";
                
                echo "<div class='warning'>";
                echo "<h5>Invalid User Data:</h5>";
                echo "<p><strong>Data:</strong> " . json_encode($testUser2) . "</p>";
                $errors2 = validateUserSchema($testUser2);
                if (empty($errors2)) {
                    echo "<p class='success'>✅ Schema validation passed</p>";
                } else {
                    echo "<p class='error'>❌ Errors: " . implode(', ', $errors2) . "</p>";
                }
                echo "</div>";
                ?>
            </div>
        </div>

        <div class="info">
            <h3>📝 สรุปความรู้ที่ได้เรียน:</h3>
            <ul>
                <li><strong>JWT Authentication:</strong> การสร้างและตรวจสอบ JSON Web Token</li>
                <li><strong>REST API Design:</strong> การออกแบบ API ตาม REST principles</li>
                <li><strong>CRUD Operations:</strong> Create, Read, Update, Delete through API</li>
                <li><strong>File Upload:</strong> การอัพโหลดไฟล์เดี่ยวและหลายไฟล์พร้อมกัน</li>
                <li><strong>Input Validation:</strong> การตรวจสอบข้อมูลเข้า</li>
                <li><strong>Error Handling:</strong> การจัดการข้อผิดพลาดแบบมาตรฐาน</li>
                <li><strong>CORS Handling:</strong> การจัดการ Cross-Origin Resource Sharing</li>
                <li><strong>JSON Processing:</strong> การประมวลผล JSON data</li>
                <li><strong>Authentication Middleware:</strong> การตรวจสอบสิทธิ์</li>
                <li><strong>API Response Formatting:</strong> การจัดรูปแบบ response</li>
            </ul>
            
            <h3>🔗 ลิงก์ที่เกี่ยวข้อง:</h3>
            <p>
                <a href="../" class="btn">🏠 กลับหน้าหลัก</a>
                <a href="../lab04-memcache/" class="btn">⬅️ Lab 04: Memcache</a>
                <a href="api.php" class="btn" target="_blank">🔗 REST API Endpoint</a>
            </p>
        </div>
    </div>

    <script>
    let currentToken = '';
    
    // Tab Management
    function showTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Show selected tab content
        document.getElementById(tabId).classList.add('active');
        
        // Add active class to clicked tab
        event.target.classList.add('active');
    }
    
    // File name display
    function updateFileName(inputId, displayId) {
        const input = document.getElementById(inputId);
        const display = document.getElementById(displayId);
        if (input.files.length > 0) {
            display.textContent = `Selected: ${input.files[0].name}`;
        }
    }
    
    function updateFileNames(inputId, displayId) {
        const input = document.getElementById(inputId);
        const display = document.getElementById(displayId);
        if (input.files.length > 0) {
            const fileNames = Array.from(input.files).map(file => file.name);
            display.textContent = `Selected ${input.files.length} files: ${fileNames.join(', ')}`;
        }
    }
    
    // API Functions
    async function apiCall(endpoint, method = 'GET', data = null, token = null) {
        const url = `api.php/${endpoint}`;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };
        
        if (token) {
            options.headers['Authorization'] = `Bearer ${token}`;
        }
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            return { success: false, message: error.message };
        }
    }
    
    // Authentication Functions
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const data = {
            username: document.getElementById('reg_username').value,
            email: document.getElementById('reg_email').value,
            password: document.getElementById('reg_password').value
        };
        
        const result = await apiCall('auth/register', 'POST', data);
        
        const responseDiv = document.getElementById('register-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
        
        if (result.success && result.data.access_token) {
            currentToken = result.data.access_token;
            // Auto-fill tokens in other forms
            document.getElementById('test_token').value = currentToken;
            document.getElementById('post_token').value = currentToken;
            document.getElementById('upload_token').value = currentToken;
            document.getElementById('multi_upload_token').value = currentToken;
            document.getElementById('files_token').value = currentToken;
        }
    });
    
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const data = {
            username: document.getElementById('login_username').value,
            password: document.getElementById('login_password').value
        };
        
        const result = await apiCall('auth/login', 'POST', data);
        
        const responseDiv = document.getElementById('login-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
        
        if (result.success && result.data.access_token) {
            currentToken = result.data.access_token;
            // Auto-fill tokens in other forms
            document.getElementById('test_token').value = currentToken;
            document.getElementById('post_token').value = currentToken;
            document.getElementById('upload_token').value = currentToken;
            document.getElementById('multi_upload_token').value = currentToken;
            document.getElementById('files_token').value = currentToken;
        }
    });
    
    async function testToken() {
        const token = document.getElementById('test_token').value;
        const result = await apiCall('users', 'GET', null, token);
        
        const responseDiv = document.getElementById('token-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
    }
    
    async function getProfile() {
        const token = document.getElementById('test_token').value;
        // ใช้ user_id จาก token หรือใช้ endpoint อื่น
        const result = await apiCall('users', 'GET', null, token);
        
        const responseDiv = document.getElementById('token-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
    }
    
    // Posts Functions
    async function getPosts() {
        const page = document.getElementById('posts_page').value;
        const limit = document.getElementById('posts_limit').value;
        const category = document.getElementById('posts_category').value;
        
        let query = `posts?page=${page}&limit=${limit}`;
        if (category) {
            query += `&category=${category}`;
        }
        
        const result = await fetch(`api.php/${query}`).then(r => r.json());
        
        const responseDiv = document.getElementById('posts-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
    }
    
    document.getElementById('createPostForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const token = document.getElementById('post_token').value;
        const data = {
            title: document.getElementById('post_title').value,
            content: document.getElementById('post_content').value,
            category_id: document.getElementById('post_category').value || null
        };
        
        const result = await apiCall('posts', 'POST', data, token);
        
        const responseDiv = document.getElementById('create-post-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
    });
    
    async function getAPIInfo() {
        const result = await apiCall('');
        
        const responseDiv = document.getElementById('api-info-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
    }
    
    // File Upload Functions
    document.getElementById('singleUploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const token = document.getElementById('upload_token').value;
        const fileInput = document.getElementById('single-file');
        
        if (!fileInput.files[0]) {
            alert('Please select a file');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        
        const options = {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            body: formData
        };
        
        try {
            const response = await fetch('api.php/upload', options);
            const result = await response.json();
            
            const responseDiv = document.getElementById('single-upload-response');
            responseDiv.style.display = 'block';
            responseDiv.textContent = JSON.stringify(result, null, 2);
        } catch (error) {
            console.error('Upload error:', error);
        }
    });
    
    document.getElementById('multipleUploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const token = document.getElementById('multi_upload_token').value;
        const fileInput = document.getElementById('multiple-files');
        
        if (fileInput.files.length === 0) {
            alert('Please select files');
            return;
        }
        
        const formData = new FormData();
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('files[]', fileInput.files[i]);
        }
        
        const options = {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            body: formData
        };
        
        try {
            const response = await fetch('api.php/upload', options);
            const result = await response.json();
            
            const responseDiv = document.getElementById('multiple-upload-response');
            responseDiv.style.display = 'block';
            responseDiv.textContent = JSON.stringify(result, null, 2);
        } catch (error) {
            console.error('Upload error:', error);
        }
    });
    
    async function getMyFiles() {
        const token = document.getElementById('files_token').value;
        const result = await apiCall('files', 'GET', null, token);
        
        const responseDiv = document.getElementById('files-response');
        responseDiv.style.display = 'block';
        responseDiv.textContent = JSON.stringify(result, null, 2);
    }
    </script>
</body>
</html>