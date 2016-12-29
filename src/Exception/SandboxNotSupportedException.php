<?php

namespace SandboxRE\Exception;

use RuntimeException;
use Exception;

class SandboxNotSupportedException extends RuntimeException
{
    private $type;

    public function __construct($message, $type, $code = 0, Exception $previous = null) {
        $this->type = $type;

        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message} : \"{$this->type}\" \n";
    }
}