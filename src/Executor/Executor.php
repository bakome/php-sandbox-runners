<?php

namespace SandboxRE\Executor;

use SandboxRE\Core\SandboxResult;

interface Executor
{
    /**
     * Execute php code snippet
     *
     * @param string $codeSnippet
     *
     * @return SandboxResult
     *
     */
    public function do(string $codeSnippet): SandboxResult;
}