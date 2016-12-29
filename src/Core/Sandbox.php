<?php

namespace SandboxRE\Core;

use SandboxRE\Exception\ExecutionFailException;

interface Sandbox
{
    /**
     * Execute code in sandbox
     *
     * @param string  $codeSnippet
     *
     * @return SandboxResult
     *
     * @throws ExecutionFailException
     */
    public function execute(string $codeSnippet) : SandboxResult;
}