<?php
/**
 * 缓存类
 * @author zhuyl369
 * @url http://www.blesslin.com
 */
class Cache
{
    private $cache_path;
    private $cache_expire;
    /**
     * 实例化对象
     * @access public
     * @param int $exp_time     缓存时间（秒）
     * @param string  $secret   缓存文件路径
     * @throws Exception
     */
    public function __construct($exp_time = 3600, $path = "cache/")
    {
        $this->cache_expire = $exp_time;
        $this->cache_path = $path;
        if (!is_dir($path)) {
            mkdir($path, 0777, true) ?'':die('创建缓存目录失败');
        }
    }

    private function fileName($key)
    {
        return $this->cache_path . md5($key);
    }
    /**
     * 写入缓存信息
     * @access public
     * @param string $key   缓存文件键名
     * @param all $data         缓存数据
     * @return boolean
     */
    public function put($key, $data)
    {
        $values = serialize($data);
        $filename = $this->fileName($key);
        $file = fopen($filename, 'w');
        if ($file) {
            fwrite($file, $values);
            fclose($file);
            return true;
        } else{
            return false;
        }
    }
    /**
     * 读取缓存信息
     * @access public
     * @param string $key   缓存文件键名
     * @return boolean
     */
    public function get($key)
    {
        $filename = $this->fileName($key);
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }
        if (time() < (filemtime($filename) + $this->cache_expire)) {
            $file = fopen($filename, "r");
            if ($file) {
                $data = fread($file, filesize($filename));
                fclose($file);
                return unserialize($data);
            } else return false;
        } else return false;
    }
}
