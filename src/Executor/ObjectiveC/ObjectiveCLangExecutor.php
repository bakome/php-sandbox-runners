<?php

namespace SandboxRE\Executor\ObjectiveC;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Executor\DockerExecutor;
use SandboxRE\Executor\Executor;
use SandboxRE\Helpers\TerminalHelper;

class ObjectiveCLangExecutor extends DockerExecutor implements Executor
{
    private $baseDir = '/tmp/php-sandbox-runners/';

    public function __construct()
    {
        if(!TerminalHelper::commandExist("clang")) {
            throw new ObjectiveClangCommandMissingException();
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
        $file = $this->createTempCodeFile();
        $this->createCodeFileContent($file, $codeSnippet);
        $compiledName = $this->baseDir . $this->generateUniqueName();

        try {
            shell_exec("clang -I/usr/include/GNUstep -fconstant-string-class=NSConstantString -D_NATIVE_OBJC_EXCEPTIONS  -lobjc -w $file -o $compiledName -lgnustep-base");
            $results = shell_exec("$compiledName");
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        } finally {
            $this->clearFiles($file, $compiledName);
        }

        return new SandboxResult($results);
    }

    private function clearFiles($file, $compiledName)
    {
        @unlink($file);
        @unlink($compiledName);

        return true;
    }

    private function createTempCodeFile()
    {
        $file = $this->baseDir . "objective-c" . $this->generateUniqueName() . ".m";

        if(!is_dir($this->baseDir)) {
            mkdir($this->baseDir);
        }

        return $file;
    }

    private function createCodeFileContent($file, $codeSnippet)
    {
        $handle = fopen($file, "w");
        fwrite($handle, $codeSnippet);
        fclose($handle);
    }

    private function generateUniqueName()
    {
        return md5(uniqid(rand(), true));
    }
}