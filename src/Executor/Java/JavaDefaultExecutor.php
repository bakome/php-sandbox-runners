<?php

namespace SandboxRE\Executor\Java;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Exception\JavaCompilerCommandMissingException;
use SandboxRE\Exception\JavaRunnerCommandMissingException;
use SandboxRE\Executor\DockerExecutor;
use SandboxRE\Executor\Executor;
use SandboxRE\Helpers\TerminalHelper;

class JavaDefaultExecutor extends DockerExecutor implements Executor
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
        $fileName = $this->generateUniqueName();
        $file = $this->createTempCodeFile($fileName);

        $command = $this->createCodeFileContent($file, $codeSnippet, $fileName);
        $command .= " &&  javac $file.java";
        $command .= " &&  java -cp $this->baseDir $fileName";

        try {
            $results = $this->execute($command, $this->options['execWaitTime']);
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        }

        return new SandboxResult($results);
    }

    private function createTempCodeFile($fileName)
    {
        return $this->baseDir . $fileName;
    }

    private function createCodeFileContent($file, $codeSnippet, $fileName)
    {
        $code = str_replace("public class Main", "public class $fileName", $codeSnippet);
        $code = str_replace("'", "\"", $code);
        $code = str_replace("\\n", "\\\\n", $code);
        $code = trim(preg_replace('/\s\s+/', ' ', $code));

        return "mkdir -p {$this->baseDir} && touch $file.java && echo '$code' >> $file.java";
    }

    private function generateUniqueName()
    {
        return "java" . md5(uniqid(rand(), true));
    }
}