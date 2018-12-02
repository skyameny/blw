<?php
namespace core\exception;

use think\exception\Handle;
use think\Config;
use think\Request;
use think\Response;
use think\exception\HttpException;

class SystemHandle extends Handle
{

    public function render(\Exception $e)
    {
        if ($this->render && $this->render instanceof \Closure) {
            $result = call_user_func_array($this->render, [$e]);
            if ($result) {
                return $result;
            }
        }
        
        if ($e instanceof HttpException) {
            return $this->renderHttpException($e);
        }else if($e instanceof CommonException){
            return $this->renderCommonException($e);
        }else {
            return $this->convertExceptionToResponse($e); 
        }
    }
    
    /**
     * 自定义异常处理
     * @param unknown $exception
     */
    public function renderCommonException(CommonException $exception)
    {
  
        if($exception->getCode()){
            $result = [
                'status' => $exception->getCode(),
                'info' => $exception->getMessage(),
                'time' => NOW_TIME,
                'data' => null // Java解析null、数组对象
            ];
            $type = Request::instance()->isAjax() ? Config::get('default_ajax_return') : Config::get('default_return_type');
            $response = Response::create($result, $type)->header([]);
            return $response;
        }
        return $this->convertExceptionToResponse($exception);
    }
}