<?php

namespace SandboxRE\Sandbox;

use SandboxRE\Core\Sandbox;
use SandboxRE\Core\SandboxResult;
use SandboxRE\Exception\ExecutionFailException;
use SandboxRE\Executor\Executor;
use Exception;

class PhpSandbox implements Sandbox
{
    private $executor;

    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * Execute code in sandbox
     *
     * @param string $codeSnippet
     *
     * @return SandboxResult
     *
     * @throws ExecutionFailException
     */
    public function execute(string $codeSnippet): SandboxResult
    {
        try {
            return $this->executor->do($codeSnippet);
        } catch (Exception $exception) {
            throw new ExecutionFailException(
                $exception->getMessage(),
                $exception->getCode()
            );
        }

        return new SandboxResult("");
    }
}