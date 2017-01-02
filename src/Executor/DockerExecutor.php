<?php

namespace SandboxRE\Executor;

use Docker\API\Model\ContainerConfig;
use Docker\Docker;

abstract class DockerExecutor
{
    protected $docker;
    protected $containerManager;

    protected $dockerImage = 'bakobako/php-sandbox-runner';

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

    public function execute(string $command, int $waitTime = 0) : array
    {
        $containerId = $this->createContainer();
        $webSocketStream = $this->attachSocket($containerId);

        try {
            $webSocketStream->write("time -f \"{{%e,%U,%S}}\" " . $command . PHP_EOL);

            $output = "";
            while (($data = $webSocketStream->read($waitTime)) != false) {
                $output .= $data;
            }

            $webSocketStream->write("exit" . PHP_EOL);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        } finally {
            $this->containerManager->kill($containerId);
            $this->containerManager->remove($containerId);
        }

        return $this->parseResult($output);
    }

    private function parseResult($resultString)
    {
        $result = [];

        if (preg_match('/{{(.*?)}}/', $resultString, $display) === 1) {
            $result = explode(",", $display[1]);
        }

        $result[] = trim(preg_replace('/{{(.*?)}}/', '', $resultString));

        return $result;
    }
}