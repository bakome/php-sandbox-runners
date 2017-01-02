<?php

namespace SandboxRE\Executor;

use SandboxRE\Core\SandboxResult;

interface Executor
{
    /**
     * Run code snippet
     *
     * @param string $codeSnippet
     *
     * @return SandboxResult
     *
     */
    public function do(string $codeSnippet): SandboxResult;

    /**
     * Execute code in sandbox
     *
     * @param string $command
     *
     * @return array
     *
     */
    public function execute(string $command): array;
}