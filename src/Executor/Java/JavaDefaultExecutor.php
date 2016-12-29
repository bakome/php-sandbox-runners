<?php

namespace SandboxRE\Executor\Java;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Exception\JavaCompilerCommandMissingException;
use SandboxRE\Exception\JavaRunnerCommandMissingException;
use SandboxRE\Executor\Executor;
use SandboxRE\Helpers\TerminalHelper;

class JavaDefaultExecutor implements Executor
{
    private $baseDir = '/tmp/php-sandbox-runners/';

    public function __construct()
    {
        if(!TerminalHelper::commandExist("javac")) {
            throw new JavaCompilerCommandMissingException();
        }

        if(!TerminalHelper::commandExist("java")) {
            throw new JavaRunnerCommandMissingException();
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
        $fileName = $this->generateUniqueName();
        $file = $this->createTempCodeFile($fileName);
        $this->createCodeFileContent($file, $codeSnippet, $fileName);
        $compiledName = $this->baseDir . $this->generateUniqueName();

        try {
            shell_exec("javac $file.java");
            $results = shell_exec("java -classpath " . escapeshellcmd($this->baseDir) . " $fileName");
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        } finally {
            $this->clearFiles($file, $compiledName);
        }

        return new SandboxResult($results);
    }

    private function clearFiles($file, $compiledName)
    {
        @unlink($file . ".java");
        @unlink($compiledName);

        return true;
    }

    private function createTempCodeFile($fileName)
    {
        $file = $this->baseDir . $fileName;

        if(!is_dir($this->baseDir)) {
            mkdir($this->baseDir);
        }

        return $file;
    }

    private function createCodeFileContent($file, $codeSnippet, $fileName)
    {
        $code = str_replace("public class Main", "public class $fileName", $codeSnippet);

        $handle = fopen($file . ".java", "w");
        fwrite($handle, $code);
        fclose($handle);
    }

    private function generateUniqueName()
    {
        return "java" . md5(uniqid(rand(), true));
    }
}