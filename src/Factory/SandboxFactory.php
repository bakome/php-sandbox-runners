<?php

namespace SandboxRE\Factory;

use SandboxRE\Core\Sandbox;
use SandboxRE\Exception\SandboxExecutorNotSupportedException;
use SandboxRE\Exception\SandboxNotSupportedException;
use SandboxRE\Executor\Java\JavaDefaultExecutor;
use SandboxRE\Executor\Javascript\JavascriptNodeExecutor;
use SandboxRE\Executor\ObjectiveC\ObjectiveCLangExecutor;
use SandboxRE\Executor\Php\PhpShellExecutor;
use SandboxRE\Sandbox\JavaSandbox;
use SandboxRE\Sandbox\JavascriptSandbox;
use SandboxRE\Sandbox\ObjectiveCSandbox;
use SandboxRE\Sandbox\PhpSandbox;

class SandboxFactory implements SandboxFactoryInterface
{
    protected $executors = [
        'php' => [
            'sandbox' => PhpSandbox::class,
            'executor' => PhpShellExecutor::class,
        ],
        'javascript' => [
            'sandbox' => JavascriptSandbox::class,
            'executor' => JavascriptNodeExecutor::class,
        ],
        'objectiveC' => [
            'sandbox' => ObjectiveCSandbox::class,
            'executor' => ObjectiveCLangExecutor::class,
        ],
        'java' => [
            'sandbox' => JavaSandbox::class,
            'executor' => JavaDefaultExecutor::class,
        ],
    ];

    public function __construct($executors = [])
    {
        $this->overrideExecutors($executors);
    }

    /**
     * Creates sandbox instance
     *
     * @param string $type
     *
     * @return Sandbox
     *
     * @throws SandboxNotSupportedException
     */
    public function make(string $type): Sandbox
    {
        try {
            $executorName = $this->executors[$type];
            $executor = new $executorName['executor'];

        } catch (\Exception $e) {
            throw new SandboxExecutorNotSupportedException(
                "Sandbox executor not supported message: {$e->getMessage()}",
                $executorName['executor']
            );
        }

        try {
            $sandboxName = $this->executors[$type];
            $sandbox = new $sandboxName['sandbox']($executor);
        } catch (\Exception $e) {
            throw new SandboxNotSupportedException(
                "Sandbox type not supported",
                $type
            );

        }

        return $sandbox;
    }

    private function overrideExecutors($executors)
    {
        foreach ($executors as $key => $value) {
            if (isset($this->executors[$key])) {
                $this->executors[$key]['executor'] = $value;
            }
        }
    }
}