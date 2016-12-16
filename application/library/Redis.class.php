<?php
namespace Yboard;
class Redis {
    public $enable = false;
    private $redis;
    private $host;
    private $port;
    private $timeout;

    public function __construct($data = null) {
        $redis = new \Redis();
        if ($redis == false) {
            return false;
        }
        $config = getConfig('redis/proxy');
        $config_array = $config->toArray();
        $this->redis = $redis;
        $rand_key = array_rand($config_array);
        $redis_server = $config_array[$rand_key];
        $this->host = $redis_server['server'];
        $this->port = $redis_server['port'];
        $this->timeout = $redis_server['timeout'] ? $redis_server['timeout'] : 2;
        $pconnect = $redis_server['pconnect'];
        
        if ($pconnect) {
            $this->pconnect();
        } else {
            $this->connect();
        }
    }

    public function pconnect() {
        try {
            if ($this->redis->pconnect($this->host, $this->port, $this->timeout)) {
                $this->enable = true;
            }
        } catch(Exception $e) {
            writelog('app_redis_error', $message = $e->getMessage());
            $this->enable = FALSE;
        }
    }

    public function connect() {
        try {
            if ($this->redis->connect($this->host, $this->port, $this->timeout)) {
                $this->enable = true;
            }
        } catch(Exception $e) {
            writelog('app_redis_error', $message = $e->getMessage());
            $this->enable = FALSE;
        }
    }

    public function close() {
        return $this->redis->close();
    }

    public function get($key) {
        $data = $this->redis->get($key);
        if (!$data) {
            return $data;
        }
        
        return json_decode($data, true);
    }

    public function set($key, $value) {
        $value = json_encode($value);
        
        return $this->redis->set($key, $value);
    }

    public function setex($key, $value, $expires = 86400) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        
        return $this->redis->setex($key, $expires, $value);
    }

    public function increment($key, $value = 1) {
        return $this->redis->incr($key, $value);
    }

    public function decrement($key, $value = 1) {
        return $this->redis->decr($key, $value);
    }

    public function lPush($key, $value) {
        return $this->redis->lPush($key, $value);
    }

    public function lRange($key, $start = 0, $stop = -1) {
        return $this->redis->lRange($key, $start, $stop);
    }
    
    public function lSet($key, $index, $value) {
        return $this->redis->lSet($key, $index, $value);
    }

    public function lRem($key, $value, $count = 0) {
        return $this->redis->lRem($key, $value, $count);
    }

    public function rPush($key, $value) {
        return $this->redis->rPush($key, $value);
    }

    public function lPop($key) {
        return $this->redis->lPop($key);
    }

    public function rPop($key) {
        return $this->redis->rPop($key);
    }

    public function lLen($key) {
        return $this->redis->lLen($key);
    }

    public function zAdd($key, $score, $value) {
        return $this->redis->zAdd($key, $score, $value);
    }

    public function zCard($key) {
        return $this->redis->zCard($key);
    }

    public function zDelete($key, $member) {
        return $this->redis->zDelete($key, $member);
    }

    public function zRange($key, $start, $end, $with_score = false) {
        return $this->redis->zRange($key, $start, $end, $with_score);
    }

    public function zRangeByScore($key, $start, $end, $options = null) {
        if (is_array($options) && !empty($options)) {
            return $this->redis->zRangeByScore($key, $start, $end, $options);
        } else {
            return $this->redis->zRangeByScore($key, $start, $end);
        }
    }

    public function zRevRangeByScore($key, $start, $end, $options = array()) {
        return $this->redis->zRevRangeByScore($key, $start, $end, $options);
    }

    public function zRemRangeByScore($key, $start, $end) {
        return $this->redis->zRemRangeByScore($key, $start, $end);
    }

    public function zRemRangeByRank($key, $start, $end) {
        return $this->redis->zRemRangeByRank($key, $start, $end);
    }

    public function zRem($key, $value) {
        return $this->redis->zRem($key, $value);
    }

    public function zSize($key) {
        return $this->redis->zSize($key);
    }

    public function zUnion($keyOutput, $arrayZSetKeys) {
        return $this->redis->zUnion($keyOutput, $arrayZSetKeys);
    }

    public function sAdd($key, $value) {
        return $this->redis->sAdd($key, $value);
    }

    public function sMembers($key) {
        return $this->redis->sMembers($key);
    }

    public function delete($key) {
        return $this->redis->delete($key);
    }

    public function hSet($key, $hashKey, $value) {
        return $this->redis->hSet($key, $hashKey, $value);
    }

    public function hExists($key, $memberKey) {
        return $this->redis->hExists($key, $memberKey);
    }

}