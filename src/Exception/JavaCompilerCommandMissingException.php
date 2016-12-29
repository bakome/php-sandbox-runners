<?php

namespace SandboxRE\Exception;

use RuntimeException;
use Exception;

class JavaCompilerCommandMissingException extends RuntimeException
{
    public function __construct($code = 0, Exception $previous = null) {
        $this->message = "Please install javac to continue!";

        parent::__construct($this->message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}