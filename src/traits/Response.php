<?php
namespace traits;
trait Response{
    protected $default_return_type='html';
    protected $mimeType = [
        'json'  => 'application/json,text/x-json,application/jsonrequest,text/json',
        'xml'   => 'application/xml,text/xml,application/x-xml',
        'js'    => 'text/javascript,application/javascript,application/x-javascript',
        'css'   => 'text/css',
        'rss'   => 'application/rss+xml',
        'yaml'  => 'application/x-yaml,text/yaml',
        'atom'  => 'application/atom+xml',
        'pdf'   => 'application/pdf',
        'text'  => 'text/plain',
        'image' => 'image/png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/webp,image/*',
        'csv'   => 'text/csv',
        'html'  => 'text/html,application/xhtml+xml,*/*',
    ];
    protected function success($msg = 'success', $data = null, $code = 0)
    {
        if(!is_string($msg) && empty($data)){
            $data=$msg;
            $msg='success';
        }
        $this->result($msg, $data, $code);
    }
    protected function error($msg = 'error', $data = null, $code = 1)
    {
        if(!is_string($msg) && empty($data)){
            $data=$msg;
            $msg='发生未知错误！';
        }
        $this->result($msg, $data, $code);
    }
    protected function result($msg, $data = null, $code = 0)
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $requestType=$this->requestType();
        switch ($code){
            case 404:
                http_response_code(404);
                break;
            case 500:
                http_response_code(500);
                break;
            case 301:
                http_response_code(301);
                break;
            default:
                http_response_code(200);
        }
        switch ($requestType){
            case 'html':
                $this->html($result);
                break;
            case 'json':
                $this->json($result);
                break;
            case 'xml':
                $this->xml($result);
                break;
            default:
                $this->html($result);
        }
        exit;
    }
    protected function html($data){
        header('Content-Type:text/html; charset=utf-8');
        $queryString=array('m'=>$this->params['m']);
        if(isset($this->params['page'])){
            $queryString['page']=$this->params['page'];
        }
        message($data['msg'], url($this->params['c'].'/'.$this->params['a'].'/'.$this->params['do'],array('m'=>$this->params['m']),true), $data['code']==0?'error':'success');
    }
    protected function xml($data){
        header('Content-Type:application/xml;charset=utf-8');
        die($data['data']);
    }
    protected function json($data){
        header('Content-Type:application/json;charset=utf-8');
        die(json_encode($data));
    }
    protected function requestType()
    {
        $accept = $_SERVER['HTTP_ACCEPT'];
        if (empty($accept)) {
            return false;
        }
        foreach ($this->mimeType as $key => $val) {
            $array = explode(',', $val);
            foreach ($array as $k => $v) {
                if (stristr($accept, $v)) {
                    return $key;
                }
            }
        }
        return false;
    }
}