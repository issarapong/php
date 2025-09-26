<?php
// Redis Configuration และ Connection Manager
class RedisConfig {
    const HOST = 'redis';
    const PORT = 6379;
    const TIMEOUT = 2.5;
    const DATABASE = 0;
}

class RedisManager {
    private static $instance = null;
    private $redis;
    private $connected = false;
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $this->redis = new Redis();
            $this->connected = $this->redis->connect(
                RedisConfig::HOST, 
                RedisConfig::PORT, 
                RedisConfig::TIMEOUT
            );
            
            if ($this->connected) {
                $this->redis->select(RedisConfig::DATABASE);
            }
        } catch (Exception $e) {
            $this->connected = false;
            throw new Exception("Redis connection failed: " . $e->getMessage());
        }
    }
    
    public function getRedis() {
        if (!$this->connected) {
            $this->connect();
        }
        return $this->redis;
    }
    
    public function isConnected() {
        return $this->connected && $this->redis->ping() === '+PONG';
    }
    
    public function getConnectionInfo() {
        if (!$this->isConnected()) {
            return null;
        }
        
        $info = $this->redis->info();
        return [
            'redis_version' => $info['redis_version'],
            'connected_clients' => $info['connected_clients'],
            'used_memory_human' => $info['used_memory_human'],
            'total_connections_received' => $info['total_connections_received'],
            'total_commands_processed' => $info['total_commands_processed'],
            'keyspace_hits' => $info['keyspace_hits'] ?? 0,
            'keyspace_misses' => $info['keyspace_misses'] ?? 0
        ];
    }
}

// Cache Manager Class
class CacheManager {
    private $redis;
    private $defaultTtl = 3600; // 1 hour
    private $keyPrefix = 'php_lab:';
    
    public function __construct() {
        $this->redis = RedisManager::getInstance()->getRedis();
    }
    
    private function buildKey($key) {
        return $this->keyPrefix . $key;
    }
    
    public function set($key, $value, $ttl = null) {
        try {
            $cacheKey = $this->buildKey($key);
            $serializedValue = serialize($value);
            $ttl = $ttl ?? $this->defaultTtl;
            
            return $this->redis->setex($cacheKey, $ttl, $serializedValue);
        } catch (Exception $e) {
            error_log("Cache set error: " . $e->getMessage());
            return false;
        }
    }
    
    public function get($key) {
        try {
            $cacheKey = $this->buildKey($key);
            $value = $this->redis->get($cacheKey);
            
            if ($value === false) {
                return null;
            }
            
            return unserialize($value);
        } catch (Exception $e) {
            error_log("Cache get error: " . $e->getMessage());
            return null;
        }
    }
    
    public function delete($key) {
        try {
            $cacheKey = $this->buildKey($key);
            return $this->redis->del($cacheKey) > 0;
        } catch (Exception $e) {
            error_log("Cache delete error: " . $e->getMessage());
            return false;
        }
    }
    
    public function exists($key) {
        try {
            $cacheKey = $this->buildKey($key);
            return $this->redis->exists($cacheKey) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getTtl($key) {
        try {
            $cacheKey = $this->buildKey($key);
            return $this->redis->ttl($cacheKey);
        } catch (Exception $e) {
            return -1;
        }
    }
    
    public function increment($key, $value = 1) {
        try {
            $cacheKey = $this->buildKey($key);
            return $this->redis->incrBy($cacheKey, $value);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function flush() {
        try {
            return $this->redis->flushDB();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getAllKeys() {
        try {
            return $this->redis->keys($this->keyPrefix . '*');
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getStats() {
        $keys = $this->getAllKeys();
        $stats = [
            'total_keys' => count($keys),
            'keys' => []
        ];
        
        foreach ($keys as $key) {
            $ttl = $this->redis->ttl($key);
            $type = $this->redis->type($key);
            $size = strlen($this->redis->get($key));
            
            $stats['keys'][] = [
                'key' => str_replace($this->keyPrefix, '', $key),
                'ttl' => $ttl,
                'type' => $type,
                'size' => $size
            ];
        }
        
        return $stats;
    }
    
    // Cache-aside pattern
    public function remember($key, $callback, $ttl = null) {
        $value = $this->get($key);
        
        if ($value === null) {
            $value = $callback();
            if ($value !== null) {
                $this->set($key, $value, $ttl);
            }
        }
        
        return $value;
    }
}

// Session Manager using Redis
class RedisSessionManager {
    private $redis;
    private $sessionPrefix = 'session:';
    private $defaultTtl = 1800; // 30 minutes
    
    public function __construct() {
        $this->redis = RedisManager::getInstance()->getRedis();
    }
    
    public function start($sessionId = null) {
        if ($sessionId === null) {
            $sessionId = $this->generateSessionId();
        }
        
        return $sessionId;
    }
    
    private function generateSessionId() {
        return bin2hex(random_bytes(32));
    }
    
    public function set($sessionId, $key, $value) {
        try {
            $sessionKey = $this->sessionPrefix . $sessionId;
            $this->redis->hSet($sessionKey, $key, serialize($value));
            $this->redis->expire($sessionKey, $this->defaultTtl);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function get($sessionId, $key = null) {
        try {
            $sessionKey = $this->sessionPrefix . $sessionId;
            
            if ($key === null) {
                // Get all session data
                $data = $this->redis->hGetAll($sessionKey);
                $result = [];
                foreach ($data as $k => $v) {
                    $result[$k] = unserialize($v);
                }
                return $result;
            } else {
                // Get specific key
                $value = $this->redis->hGet($sessionKey, $key);
                return $value ? unserialize($value) : null;
            }
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function delete($sessionId, $key = null) {
        try {
            $sessionKey = $this->sessionPrefix . $sessionId;
            
            if ($key === null) {
                // Delete entire session
                return $this->redis->del($sessionKey) > 0;
            } else {
                // Delete specific key
                return $this->redis->hDel($sessionKey, $key) > 0;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function exists($sessionId) {
        try {
            $sessionKey = $this->sessionPrefix . $sessionId;
            return $this->redis->exists($sessionKey) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function extendSession($sessionId, $ttl = null) {
        try {
            $sessionKey = $this->sessionPrefix . $sessionId;
            $ttl = $ttl ?? $this->defaultTtl;
            return $this->redis->expire($sessionKey, $ttl);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getAllSessions() {
        try {
            $keys = $this->redis->keys($this->sessionPrefix . '*');
            $sessions = [];
            
            foreach ($keys as $key) {
                $sessionId = str_replace($this->sessionPrefix, '', $key);
                $ttl = $this->redis->ttl($key);
                $data = $this->get($sessionId);
                
                $sessions[] = [
                    'session_id' => $sessionId,
                    'ttl' => $ttl,
                    'data' => $data
                ];
            }
            
            return $sessions;
        } catch (Exception $e) {
            return [];
        }
    }
}

// Counter และ Rate Limiting
class RedisCounter {
    private $redis;
    private $prefix = 'counter:';
    
    public function __construct() {
        $this->redis = RedisManager::getInstance()->getRedis();
    }
    
    public function increment($name, $amount = 1) {
        try {
            return $this->redis->incrBy($this->prefix . $name, $amount);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function decrement($name, $amount = 1) {
        try {
            return $this->redis->decrBy($this->prefix . $name, $amount);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function get($name) {
        try {
            $value = $this->redis->get($this->prefix . $name);
            return $value !== false ? (int)$value : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    public function reset($name) {
        try {
            return $this->redis->del($this->prefix . $name) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getAllCounters() {
        try {
            $keys = $this->redis->keys($this->prefix . '*');
            $counters = [];
            
            foreach ($keys as $key) {
                $name = str_replace($this->prefix, '', $key);
                $value = $this->get($name);
                $counters[$name] = $value;
            }
            
            return $counters;
        } catch (Exception $e) {
            return [];
        }
    }
}

// Rate Limiter
class RateLimiter {
    private $redis;
    private $prefix = 'rate_limit:';
    
    public function __construct() {
        $this->redis = RedisManager::getInstance()->getRedis();
    }
    
    public function isAllowed($identifier, $maxRequests, $windowSeconds) {
        try {
            $key = $this->prefix . $identifier;
            $now = time();
            $window = $now - $windowSeconds;
            
            // Remove old entries
            $this->redis->zRemRangeByScore($key, 0, $window);
            
            // Count current requests
            $currentCount = $this->redis->zCard($key);
            
            if ($currentCount < $maxRequests) {
                // Add current request
                $this->redis->zAdd($key, $now, uniqid());
                $this->redis->expire($key, $windowSeconds);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return true; // Allow on error
        }
    }
    
    public function getRemainingRequests($identifier, $maxRequests, $windowSeconds) {
        try {
            $key = $this->prefix . $identifier;
            $now = time();
            $window = $now - $windowSeconds;
            
            $this->redis->zRemRangeByScore($key, 0, $window);
            $currentCount = $this->redis->zCard($key);
            
            return max(0, $maxRequests - $currentCount);
        } catch (Exception $e) {
            return $maxRequests;
        }
    }
    
    public function getTimeToReset($identifier, $windowSeconds) {
        try {
            $key = $this->prefix . $identifier;
            $oldest = $this->redis->zRange($key, 0, 0, true);
            
            if (empty($oldest)) {
                return 0;
            }
            
            $oldestTime = array_values($oldest)[0];
            return max(0, $windowSeconds - (time() - $oldestTime));
        } catch (Exception $e) {
            return 0;
        }
    }
}
?>