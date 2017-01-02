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

    public function __construct()
    {
        parent::__construct();

//        if(!TerminalHelper::commandExist("javac")) {
//            throw new JavaCompilerCommandMissingException();
//        }
//
//        if(!TerminalHelper::commandExist("java")) {
//            throw new JavaRunnerCommandMissingException();
//        }
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
            $this->execute("javac $file.java");

            $results = $this->execute(
                "java -cp $this->baseDir $fileName"
            );

        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        } finally {
            $this->clearFiles($file, $compiledName);
        }

        return new SandboxResult($results);
    }

    private function clearFiles($file, $compiledName)
    {
        $this->execute("rm -rf $file.java");
        $this->execute("rm -rf $compiledName");

        return true;
    }

    private function createTempCodeFile($fileName)
    {
        $file = $this->baseDir . $fileName;

        $this->execute("mkdir -p $this->baseDir");

        return $file;
    }

    private function createCodeFileContent($file, $codeSnippet, $fileName)
    {
        $code = str_replace("public class Main", "public class $fileName", $codeSnippet);
        $code = str_replace("'", "\"", $code);
        $code = trim(preg_replace('/\s\s+/', ' ', $code));

        $this->execute("touch $file.java");
        $this->execute("echo '$code' >> $file.java");
    }

    private function generateUniqueName()
    {
        return "java" . md5(uniqid(rand(), true));
    }
}