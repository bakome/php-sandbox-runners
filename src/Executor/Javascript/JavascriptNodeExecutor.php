<?php

namespace SandboxRE\Executor\Javascript;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Exception\JavascriptNodeCommandMissingException;
use SandboxRE\Executor\Executor;
use SandboxRE\Helpers\TerminalHelper;

class JavascriptNodeExecutor implements Executor
{
    public function __construct()
    {
        if(!TerminalHelper::commandExist("node")) {
            throw new JavascriptNodeCommandMissingException();
        }
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
            $results = shell_exec("node -p -e '" . str_replace("'", "\"'\"", $codeSnippet) . "'");
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        }

        return new SandboxResult($results);
    }
}