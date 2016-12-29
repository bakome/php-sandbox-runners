<?php

namespace SandboxRE\Executor\Php;

use SandboxRE\Core\SandboxResult;
use Runkit_Sandbox;
use SandboxRE\Exception\PhpRunKitExtensionMissingException;

class PhpRunKitExecutor implements Executor
{
    private $runKitSandbox;

    private $extensionName = "php_runkit";

    private $safeMode = true;
    private $openBaseDir = "/tmp/php-sandbox-executor/";
    private $allowUrlFopen = false;
    private $disableFunctions = 'exec,shell_exec,passthru,system';
    private $disableClasses = '';
    private $htmlErrors = true;

    public function __construct()
    {
        if (!extension_loaded($this->extensionName)) {
            throw new PhpRunKitExtensionMissingException();
        }

        $options = [
            'safe_mode' => $this->safeMode,
            'open_basedir' => $this->openBaseDir,
            'allow_url_fopen' => $this->allowUrlFopen,
            'disable_functions' => $this->disableFunctions,
            'disable_classes' => $this->disableClasses
        ];

        $this->runKitSandbox = new Runkit_Sandbox($options);

        $this->runKitSandbox->ini_set('html_errors', $this->htmlErrors);
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
        return new SandboxResult($this->runKitSandbox->eval($codeSnippet));
    }
}