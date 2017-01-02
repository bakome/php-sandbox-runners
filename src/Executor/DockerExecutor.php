<?php

namespace SandboxRE\Executor;

use Docker\API\Model\ContainerConfig;
use Docker\Docker;

abstract class DockerExecutor
{
    protected $docker;
    protected $containerManager;

    protected $dockerImage = 'php-sandbox-runner';

    public function __construct()
    {
        $this->docker = new Docker();
        $this->containerManager = $this->docker->getContainerManager();

    }

    public function createContainer()
    {
        $containerConfig = new ContainerConfig();
        $containerConfig->setImage($this->dockerImage);
        $containerConfig->setCmd(["sh"]);
        $containerConfig->setOpenStdin(true);
        $containerConfig->setTty(false);

        $creatingResults = $this->containerManager->create($containerConfig);
        $this->containerManager->start($creatingResults->getId());

        return $creatingResults->getId();
    }

    public function attachSocket($containerID)
    {
        return $this->containerManager->attachWebsocket($containerID, [
            'stream' => true,
            'stdout' => true,
            'stderr' => true,
            'stdin' => true,
        ]);
    }

    public function execute(string $command) : string
    {
        $containerId = $this->createContainer();
        $webSocketStream = $this->attachSocket($containerId);

        $webSocketStream->write($command . PHP_EOL);

        $output = "";
        while (($data = $webSocketStream->read(1)) != false) {
            $output .= $data;
        }

        $webSocketStream->write("exit" . PHP_EOL);
        $this->containerManager->kill($containerId);
        $this->containerManager->remove($containerId);

        return trim($output);
    }
}