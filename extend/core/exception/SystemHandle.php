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
        }else if($e instanceof CoreException){
            if(Config::get("app_debug") !== true){
                return $this->renderCommonException($e);
            }
            return $this->convertExceptionToResponse($e);
        }else {
            return $this->convertExceptionToResponse($e); 
        }
    }
    
    /**
     * 自定义异常处理
     * @param unknown $exception
     */
    public function renderCommonException(CoreException $exception)
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

#粗暴的方式
//    protected function convertExceptionToResponse(Exception $exception)
//    {
//        $type = Request::instance()->isAjax() ? Config::get('default_ajax_return') : Config::get('default_return_type');
//        switch ($type){
//            case 'json':
//                if ($exception instanceof HttpException)
//                {
//                    $exception = new SystemNotSupportException($exception);
//                }
//                elseif (!$exception instanceof CoreException)
//                {
//                    $exception = new ThinkException($exception);
//                }
//                $result = [
//                    'status' => $exception->getCode(),
//                    'info' => $exception->getMessage(),
//                    'time' => NOW_TIME,
//                    'data' => '',
//                ];
//                ExLog::log('Exception catch:'.$exception, ExLog::ERROR);
//                return Response::create($result, $type)->header([]);
//                break;
//            default:
//                return parent::convertExceptionToResponse($exception);
//                break;
//        }
//    }
}