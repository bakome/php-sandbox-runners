<?php

namespace SandboxRE\Executor\Javascript;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Exception\JavascriptNodeCommandMissingException;
use SandboxRE\Executor\DockerExecutor;
use SandboxRE\Executor\Executor;
use SandboxRE\Helpers\TerminalHelper;

class JavascriptNodeExecutor extends DockerExecutor implements Executor
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
            $command = trim(preg_replace('/\s\s+/', ' ', "node --harmony -p -e '" . str_replace("'", "\"'\"", $codeSnippet) . "'"));

            $results = $this->execute($command);
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        }

        return new SandboxResult($results);
    }
}