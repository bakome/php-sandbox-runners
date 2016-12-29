<?php

namespace SandboxRE\Helpers;

class TerminalHelper
{
    public static function commandExist($command) {
        return !empty(
            shell_exec(
                sprintf("which %s", escapeshellarg($command))
            )
        );
    }
}