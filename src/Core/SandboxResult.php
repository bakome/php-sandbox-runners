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
    private $result = [];

    public function __construct($result)
    {
        $this->parseResult($result);
    }

    private function parseResult($result)
    {
        $this->result['real'] = $result[0] ?? NULL;
        $this->result['user'] = $result[1] ?? NULL;
        $this->result['system'] = $result[2] ?? NULL;
        $this->result['output'] = $result[3] ?? NULL;
    }

    public function __toString()
    {
        return "Output: {$this->result['output']}, Execution real time: {$this->result['real']}";
    }

    public function toString()
    {
        return "Output: {$this->result['output']}, Execution real time: {$this->result['real']}";
    }
}