<?php

namespace SandboxRE\Executor\Php;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Executor\DockerExecutor;
use SandboxRE\Executor\Executor;

class PhpShellExecutor extends DockerExecutor implements Executor
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute php code snippet
     *
     * @param string $codeSnippet
     *
     * @return SandboxResult
     */
    public function do(string $codeSnippet): SandboxResult
    {
        try {
            $command = trim(preg_replace('/\s\s+/', ' ', "php -r '" . str_replace("'", "\"'\"", $codeSnippet) . "'"));

            $results = $this->execute($command);
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        }

        return new SandboxResult($results);
    }
}