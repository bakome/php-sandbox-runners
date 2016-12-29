<?php
/**
 * Created by PhpStorm.
 * User: bako
 * Date: 12/29/16
 * Time: 9:29 AM
 */

namespace SandboxRE\Core;


class SandboxResult
{
    private $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function __toString()
    {
        return $this->result != NULL ? $this->result : "No result returned!!!\n";
    }

    public function toString()
    {
        return $this->result != NULL ? $this->result : "No result returned!!!\n";
    }
}