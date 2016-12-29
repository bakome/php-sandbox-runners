<?php

namespace SandboxRE\Executor\Php;

use SandboxRE\Core\SandboxResult;
use SandboxRE\Executor\Executor;

class PhpShellExecutor implements Executor
{
    private $options = 	[
        'chroot' => '/tmp/php-sandbox-runners/',
        'open_basedir' => '/tmp/php-sandbox-runners/',
        'display_errors' => 'off',
        'pass_post' => false,
        'pass_get' => false,
        'pass_session_data' => false,
        'pass_session_id' => false,
        'auto_prepend_file' => false,
        'auto_append_file' => false,
        'force_session_workaround' => true,
        'max_execution_time' => 1,
        'memory_limit' => '2M',
        'disable_functions' => 'exec,passthru,shell_exec,system,proc_open,popen
            ,curl_exec,curl_multi_exec,parse_ini_file,show_source,pcntl_fork,pcntl_exec
            ,session_start,phpinfo,ini_set
         ',
        'directory_protection' => true,
        'directory_protection_allow_tmp' => true,
        'use_apc' => false,
        'log_to_file' => false,
        'log_to_array' => true,
    ];

    public function __construct()
    {
        $this->createChrootDirectory();
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
        try {
            $results = shell_exec("php {$this->makeOptions()} -r '" . str_replace("'", "\"'\"", $codeSnippet) . "'");
        } catch (\Exception $e) {
            $results = $e->getCode . " : " . $e->getMessage();
        }

        return new SandboxResult($results);
    }

    private function makeOptions()
    {
        $optionsString = " ";

        foreach ($this->options as $option => $value) {
            $optionsString .= " -d $option=\"$value\" ";
        }

        return $optionsString;
    }

    private function createChrootDirectory()
    {
        if (!is_dir($this->options['chroot'])) {
            mkdir($this->options['chroot']);
        }

        return true;
    }
}