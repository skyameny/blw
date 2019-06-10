<?php
namespace core\exception;

use think\Exception;

class CoreException extends Exception
{
    protected $errorcode = STATUS_CODE_SYSTEM_ERROR;

    public function __construct($code = 0, $message = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if (empty($code))
        {
            $this->code = $this->errorcode;
        }
        if (empty($message))
        {
            $this->message = $this->getCodeMessage();
        }
    }

    protected function getCodeMessage()
    {
        return config(ERROR_PREFIX . $this->code);
    }

    public function __toString()
    {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n
        {$this->getTraceAsString()}";
    }
}
