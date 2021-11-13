<?php
namespace traits;
trait Request{
    protected $request,$params,$W,$w;
    public function __construct()
    {
        global $_GPC,$_W;
        $this->request = $this->params = $_GPC;
        $this->W = $this->w = $_W;
    }
    public function isGet(){
        return !($this->W['ispost']);
    }
    public function isPost(){
        return $this->W['ispost'];
    }
    public function isAjax(){
        return $this->W['isajax'];
    }
    public function isHttps(){
        return $this->W['ishttps'];
    }
    public function queryArray(){
        if(!empty($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0){
            parse_str($_SERVER['QUERY_STRING'],$query);
            return $query;
        }else{
            return array();
        }
    }
    public function isWeb(){
        $query=$this->queryArray();
        if(is_array($query) && isset($query['c'])){
            return $query['c'] == 'site'?true:false;
        }
        return false;
    }
    public function isMobile(){
        $query=$this->queryArray();
        if(is_array($query) && isset($query['c'])){
            return $query['c'] == 'entry'?true:false;
        }
        return false;
    }
    public function isWxapp(){
        $query=$this->queryArray();
        if(is_array($query) && isset($query['c'])){
            return $query['c'] == 'wxapp'?true:false;
        }
        return false;
    }

    public function url($do=null,$params=null,$domain=true){
        if(empty($do)){
            $do=$this->params['do'];
        }
        $queryString=array('m'=>$this->params['m']);
        if(!empty($params) && is_array($params)){
            $queryString=array_merge($queryString,$params);
        }else if(!empty($params) && is_string($params) && strripos($params,'=') && strripos($params,'=') < strlen($params)){
            $strParams=array();
            parse_str($params,$strParams);
            $queryString=array_merge($queryString,$strParams);
        }
        if($this->isWeb()){
            return wurl($this->params['c'].'/'.$this->params['a'].'/'.$do,$queryString,$domain);
        }else{
            return murl($this->params['c'].'/'.$this->params['a'].'/'.$do,$queryString,true,$domain);
        }
    }
    public function wurl($do=null,$params=null,$domain=true){
        if(empty($do)){
            $do=$this->params['do'];
        }
        $queryString=array('m'=>$this->params['m']);
        if(!empty($params) && is_array($params)){
            $queryString=array_merge($queryString,$params);
        }else if(!empty($params) && is_string($params) && strripos($params,'=') && strripos($params,'=') < strlen($params)){
            $strParams=array();
            parse_str($params,$strParams);
            $queryString=array_merge($queryString,$strParams);
        }
        return url($this->params['c'].'/'.$this->params['a'].'/'.$do,$queryString,$domain);
    }
    public function murl($do=null,$params=null,$domain=true){
        if(empty($do)){
            $do=$this->params['do'];
        }
        $queryString=array('m'=>$this->params['m']);
        if(!empty($params) && is_array($params)){
            $queryString=array_merge($queryString,$params);
        }else if(!empty($params) && is_string($params) && strripos($params,'=') && strripos($params,'=') < strlen($params)){
            $strParams=array();
            parse_str($params,$strParams);
            $queryString=array_merge($queryString,$strParams);
        }
        return murl($this->params['c'].'/'.$this->params['a'].'/'.$do,$queryString,true,$domain);
    }
    public function view($filename=''){
        global $_W;
        if(empty($filename)){
			$filename=$this->isWeb()?'web/'.$this->params['do']:'mobile/'.$this->params['do'];
        }
        $name = strtolower($this->modulename);
        $defineDir = dirname($this->__define);
        if (defined('IN_SYS')) {
            $source = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$filename}.html";
            $compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/{$filename}.tpl.php";
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/template/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$filename}.html";
            }
        } else {
            $source = IA_ROOT . "/app/themes/{$_W['template']}/{$name}/{$filename}.html";
            $compile = IA_ROOT . "/data/tpl/app/{$_W['template']}/{$name}/{$filename}.tpl.php";
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/default/{$name}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/template/mobile/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/template/wxapp/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/template/webapp/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                if (in_array($filename, array('header', 'footer', 'slide', 'toolbar', 'message'))) {
                    $source = IA_ROOT . "/app/themes/default/common/{$filename}.html";
                } else {
                    $source = IA_ROOT . "/app/themes/default/{$filename}.html";
                }
            }
        }

        if (!is_file($source)) {
            $this->error("错误：模板文件{$filename}不存在！");
        }
        return include $this->template($filename);
    }
}