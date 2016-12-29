<?php

namespace SandboxRE\Exception;

use RuntimeException;
use Exception;

class SandboxExecutorNotSupportedException extends RuntimeException
{
    private $executor;

    public function __construct($message, $executor, $code = 0, Exception $previous = null) {
        $this->executor = $executor;

        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message} : \"{$this->executor}\" \n";
    }
}