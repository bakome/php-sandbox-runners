<?php

namespace SandboxRE\Executor;

use Docker\API\Model\ContainerConfig;
use Docker\Docker;

abstract class DockerExecutor
{
    protected $docker;
    protected $containerManager;
    protected $webSocketStream;

    protected $dockerImage = 'php-sandbox-runner';
    protected $sandboxDockerContainerName = 'php-sandbox-container-runner';

    public function __construct()
    {
        $this->docker = new Docker();

        $this->containerManager = $this->docker->getContainerManager();

        $containerConfig = new ContainerConfig();
        $containerConfig->setImage($this->dockerImage);
        $containerConfig->setCmd(["sh"]);
        $containerConfig->setOpenStdin(true);
        $containerConfig->setTty(true);

        try{
            $container = $this->containerManager->find($this->sandboxDockerContainerName);
            $isRunning = $container->getState()->getRunning();
        } catch (\Exception $exception) {
            $isRunning = false;
        }

        if(!$isRunning) {
            try{
                $this->containerManager->create(
                    $containerConfig,
                    ['name' => $this->sandboxDockerContainerName]
                );
            } catch (\Exception $exception) {

            }

            $this->containerManager->start($this->sandboxDockerContainerName);
        }

        $this->webSocketStream = $this->containerManager->attachWebsocket($this->sandboxDockerContainerName, [
            'stream' => true,
            'stdout' => true,
            'stderr' => true,
            'stdin' => true,
        ]);
    }

    public function execute(string $command) : string
    {
        $this->clearReadBuffer();
        $this->webSocketStream->write($command . PHP_EOL);

        $output = "";
        while (($data = $this->webSocketStream->read(1, 200000)) != false) {
            $output .= $data;
        }

        return trim(str_replace($command, "", substr($output, 0, -2)));
    }

    private function clearReadBuffer()
    {
        while (($this->webSocketStream->read()) != false);
    }
}