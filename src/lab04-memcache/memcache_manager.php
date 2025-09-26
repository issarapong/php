<?php
// Memcache Configuration และ Connection Manager
class MemcacheConfig {
    const HOST = 'memcached';
    const PORT = 11211;
    const TIMEOUT = 2;
    const PERSISTENT = true;
}

class MemcacheManager {
    private static $instance = null;
    private $memcached;
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
            if (class_exists('Memcached')) {
                $this->memcached = new Memcached();
                $this->memcached->addServer(MemcacheConfig::HOST, MemcacheConfig::PORT);
                
                // ตั้งค่า options
                $this->memcached->setOptions([
                    Memcached::OPT_CONNECT_TIMEOUT => MemcacheConfig::TIMEOUT * 1000,
                    Memcached::OPT_POLL_TIMEOUT => MemcacheConfig::TIMEOUT * 1000,
                    Memcached::OPT_RECV_TIMEOUT => MemcacheConfig::TIMEOUT * 1000000,
                    Memcached::OPT_SEND_TIMEOUT => MemcacheConfig::TIMEOUT * 1000000,
                    Memcached::OPT_COMPRESSION => true,
                    Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_PHP,
                    Memcached::OPT_BINARY_PROTOCOL => true,
                    Memcached::OPT_NO_BLOCK => true,
                    Memcached::OPT_TCP_NODELAY => true
                ]);
                
                // ทดสอบการเชื่อมต่อ
                $this->connected = ($this->memcached->getVersion() !== false);
                
            } elseif (class_exists('Memcache')) {
                $this->memcached = new Memcache();
                $this->connected = $this->memcached->connect(
                    MemcacheConfig::HOST, 
                    MemcacheConfig::PORT, 
                    MemcacheConfig::TIMEOUT
                );
            } else {
                throw new Exception("Memcache extensions not available");
            }
            
        } catch (Exception $e) {
            $this->connected = false;
            throw new Exception("Memcache connection failed: " . $e->getMessage());
        }
    }
    
    public function getMemcache() {
        if (!$this->connected) {
            $this->connect();
        }
        return $this->memcached;
    }
    
    public function isConnected() {
        try {
            if (!$this->connected) {
                return false;
            }
            
            if ($this->memcached instanceof Memcached) {
                $stats = $this->memcached->getStats();
                return !empty($stats);
            } elseif ($this->memcached instanceof Memcache) {
                return $this->memcached->getStats() !== false;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getStats() {
        if (!$this->isConnected()) {
            return null;
        }
        
        try {
            if ($this->memcached instanceof Memcached) {
                $stats = $this->memcached->getStats();
                $serverKey = MemcacheConfig::HOST . ':' . MemcacheConfig::PORT;
                return isset($stats[$serverKey]) ? $stats[$serverKey] : null;
            } elseif ($this->memcached instanceof Memcache) {
                return $this->memcached->getStats();
            }
        } catch (Exception $e) {
            return null;
        }
        
        return null;
    }
    
    public function getVersion() {
        if (!$this->isConnected()) {
            return null;
        }
        
        try {
            if ($this->memcached instanceof Memcached) {
                $version = $this->memcached->getVersion();
                $serverKey = MemcacheConfig::HOST . ':' . MemcacheConfig::PORT;
                return isset($version[$serverKey]) ? $version[$serverKey] : null;
            } elseif ($this->memcached instanceof Memcache) {
                return $this->memcached->getVersion();
            }
        } catch (Exception $e) {
            return null;
        }
        
        return null;
    }
}

// Memcache Cache Manager
class MemcacheCacheManager {
    private $memcache;
    private $defaultTtl = 3600; // 1 hour
    private $keyPrefix = 'php_lab_mc:';
    private $compression = true;
    
    public function __construct() {
        $this->memcache = MemcacheManager::getInstance()->getMemcache();
    }
    
    private function buildKey($key) {
        return $this->keyPrefix . $key;
    }
    
    public function set($key, $value, $ttl = null) {
        try {
            $cacheKey = $this->buildKey($key);
            $ttl = $ttl ?? $this->defaultTtl;
            
            if ($this->memcache instanceof Memcached) {
                return $this->memcache->set($cacheKey, $value, $ttl);
            } elseif ($this->memcache instanceof Memcache) {
                $flags = $this->compression ? MEMCACHE_COMPRESSED : 0;
                return $this->memcache->set($cacheKey, $value, $flags, $ttl);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Memcache set error: " . $e->getMessage());
            return false;
        }
    }
    
    public function get($key) {
        try {
            $cacheKey = $this->buildKey($key);
            
            if ($this->memcache instanceof Memcached) {
                $result = $this->memcache->get($cacheKey);
                return ($this->memcache->getResultCode() === Memcached::RES_SUCCESS) ? $result : null;
            } elseif ($this->memcache instanceof Memcache) {
                $result = $this->memcache->get($cacheKey);
                return ($result !== false) ? $result : null;
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Memcache get error: " . $e->getMessage());
            return null;
        }
    }
    
    public function delete($key) {
        try {
            $cacheKey = $this->buildKey($key);
            
            if ($this->memcache instanceof Memcached) {
                return $this->memcache->delete($cacheKey);
            } elseif ($this->memcache instanceof Memcache) {
                return $this->memcache->delete($cacheKey);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Memcache delete error: " . $e->getMessage());
            return false;
        }
    }
    
    public function increment($key, $value = 1) {
        try {
            $cacheKey = $this->buildKey($key);
            
            if ($this->memcache instanceof Memcached) {
                $result = $this->memcache->increment($cacheKey, $value);
                if ($result === false) {
                    // Key doesn't exist, create it
                    $this->set($key, $value);
                    return $value;
                }
                return $result;
            } elseif ($this->memcache instanceof Memcache) {
                $result = $this->memcache->increment($cacheKey, $value);
                if ($result === false) {
                    $flags = $this->compression ? MEMCACHE_COMPRESSED : 0;
                    $this->memcache->set($cacheKey, $value, $flags, $this->defaultTtl);
                    return $value;
                }
                return $result;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function decrement($key, $value = 1) {
        try {
            $cacheKey = $this->buildKey($key);
            
            if ($this->memcache instanceof Memcached) {
                return $this->memcache->decrement($cacheKey, $value);
            } elseif ($this->memcache instanceof Memcache) {
                return $this->memcache->decrement($cacheKey, $value);
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function flush() {
        try {
            if ($this->memcache instanceof Memcached) {
                return $this->memcache->flush();
            } elseif ($this->memcache instanceof Memcache) {
                return $this->memcache->flush();
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getMultiple($keys) {
        try {
            $cacheKeys = array_map([$this, 'buildKey'], $keys);
            
            if ($this->memcache instanceof Memcached) {
                $results = $this->memcache->getMulti($cacheKeys);
                if ($results === false) return [];
                
                // Remove prefix from keys
                $cleanResults = [];
                foreach ($results as $key => $value) {
                    $originalKey = str_replace($this->keyPrefix, '', $key);
                    $cleanResults[$originalKey] = $value;
                }
                return $cleanResults;
            } elseif ($this->memcache instanceof Memcache) {
                // Memcache doesn't have getMulti, simulate it
                $results = [];
                foreach ($keys as $key) {
                    $value = $this->get($key);
                    if ($value !== null) {
                        $results[$key] = $value;
                    }
                }
                return $results;
            }
            
            return [];
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function setMultiple($items, $ttl = null) {
        try {
            $ttl = $ttl ?? $this->defaultTtl;
            $success = true;
            
            if ($this->memcache instanceof Memcached) {
                $cacheItems = [];
                foreach ($items as $key => $value) {
                    $cacheItems[$this->buildKey($key)] = $value;
                }
                return $this->memcache->setMulti($cacheItems, $ttl);
            } elseif ($this->memcache instanceof Memcache) {
                foreach ($items as $key => $value) {
                    if (!$this->set($key, $value, $ttl)) {
                        $success = false;
                    }
                }
                return $success;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function touch($key, $ttl) {
        try {
            $cacheKey = $this->buildKey($key);
            
            if ($this->memcache instanceof Memcached) {
                return $this->memcache->touch($cacheKey, $ttl);
            } elseif ($this->memcache instanceof Memcache) {
                // Memcache doesn't have touch, get and set again
                $value = $this->get($key);
                if ($value !== null) {
                    return $this->set($key, $value, $ttl);
                }
                return false;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
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
    
    public function getExtensionType() {
        if ($this->memcache instanceof Memcached) {
            return 'Memcached';
        } elseif ($this->memcache instanceof Memcache) {
            return 'Memcache';
        }
        return 'Unknown';
    }
}

// Distributed Cache Manager for multiple servers
class DistributedMemcacheManager {
    private $servers = [];
    private $memcached;
    private $connected = false;
    
    public function __construct($servers = null) {
        if ($servers === null) {
            $servers = [
                ['host' => MemcacheConfig::HOST, 'port' => MemcacheConfig::PORT, 'weight' => 100]
            ];
        }
        
        $this->servers = $servers;
        $this->connect();
    }
    
    private function connect() {
        try {
            if (!class_exists('Memcached')) {
                throw new Exception("Memcached extension required for distributed caching");
            }
            
            $this->memcached = new Memcached('distributed_pool');
            
            // Add servers only if not already added
            if (empty($this->memcached->getServerList())) {
                foreach ($this->servers as $server) {
                    $this->memcached->addServer(
                        $server['host'],
                        $server['port'],
                        $server['weight'] ?? 100
                    );
                }
            }
            
            // Configure for distribution
            $this->memcached->setOptions([
                Memcached::OPT_DISTRIBUTION => Memcached::DISTRIBUTION_CONSISTENT,
                Memcached::OPT_HASH => Memcached::HASH_CRC,
                Memcached::OPT_LIBKETAMA_COMPATIBLE => true,
                Memcached::OPT_COMPRESSION => true,
                Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_PHP
            ]);
            
            $this->connected = true;
            
        } catch (Exception $e) {
            $this->connected = false;
            throw new Exception("Distributed Memcache connection failed: " . $e->getMessage());
        }
    }
    
    public function set($key, $value, $ttl = 3600) {
        try {
            if (!$this->connected) {
                return false;
            }
            return $this->memcached->set($key, $value, $ttl);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function get($key) {
        try {
            if (!$this->connected) {
                return null;
            }
            $result = $this->memcached->get($key);
            return ($this->memcached->getResultCode() === Memcached::RES_SUCCESS) ? $result : null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function delete($key) {
        try {
            if (!$this->connected) {
                return false;
            }
            return $this->memcached->delete($key);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getServerStats() {
        try {
            if (!$this->connected) {
                return [];
            }
            return $this->memcached->getStats();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getServerByKey($key) {
        try {
            if (!$this->connected) {
                return null;
            }
            return $this->memcached->getServerByKey($key);
        } catch (Exception $e) {
            return null;
        }
    }
}
?>