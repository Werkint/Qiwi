<?php
namespace Werkint\Qiwi\Status;

class Status
{

    protected static $messageCodes = array(
        0 => null,
    );

    protected $code;
    protected $message;

    public function __construct($code)
    {
        $this->code = $code;
        $this->message = array_key_exists($code, static::$messageCodes) ? static::$messageCodes[$code] : 'Error ' . $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

}