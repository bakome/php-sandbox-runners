<?php

namespace SandboxRE\Factory;

use SandboxRE\Core\Sandbox;
use SandboxRE\Exception\SandboxNotSupportedException;

interface SandboxFactoryInterface
{
    /**
     * Creates sandbox instance
     *
     * @param string  $type
     *
     * @return Sandbox
     *
     * @throws SandboxNotSupportedException
     */
    public function make(string $type) : Sandbox;
}