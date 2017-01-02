<?php

namespace SandboxRE\Executor\ObjectiveC;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Executor\DockerExecutor;
use SandboxRE\Executor\Executor;

class ObjectiveCLangExecutor extends DockerExecutor implements Executor
{
    private $baseDir = '/root/runners/';

    private $options = [
        'execWaitTime' => 2
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge(
            $this->options,
            $options
        );

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
        $file = $this->createTempCodeFile();
        $compiledName = $this->baseDir . $this->generateUniqueName();

        $command = $this->createCodeFileContent($file, $codeSnippet);
        $command .= " &&  clang -I/usr/include/GNUstep -fconstant-string-class=NSConstantString -D_NATIVE_OBJC_EXCEPTIONS  -lobjc -w $file -o $compiledName -lgnustep-base";
        $command .= " &&  $compiledName";

        try {
            $results = $this->execute($command, $this->options['execWaitTime']);
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        }

        return new SandboxResult($results);
    }

    private function createTempCodeFile()
    {
        return $this->baseDir . "objective-c"  . $this->generateUniqueName() . ".m";
    }

    private function createCodeFileContent($file, $codeSnippet)
    {
        $code = str_replace("'", "\"", $codeSnippet);
        $code = str_replace("\\n", "\\\\n", $code);

        return "mkdir -p {$this->baseDir} && touch $file && echo '$code' >> $file";
    }

    private function generateUniqueName()
    {
        return md5(uniqid(rand(), true));
    }
}