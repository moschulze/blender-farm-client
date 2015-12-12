<?php

namespace MoSchulze\BlenderFarmClient;

use Monolog\Logger;

class Client
{
    /**
     * @var Api
     */
    private $api;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Blender
     */
    private $blender;

    public function run()
    {
        $task = $this->api->requestTask();

        while(!is_null($task)) {
            $this->logger->addInfo('project ' . $task->projectId . ', frame ' . $task->frameNumber);

            if(!$this->fileRepository->hasFreshestProjectFile($task->projectId, $task->projectMd5)) {
                $this->logger->addInfo('downloading file for project ' . $task->projectId);
                $filePath = $this->api->requestProjectFileForTask($task);
                $this->fileRepository->putProjectFile($task->projectId, $filePath);
            }

            $this->logger->addInfo('rendering');
            $renderingResult = $this->blender->renderFrame($task);

            $this->logger->addInfo('uploading image');
            $this->api->uploadRenderingResult($renderingResult);

            unlink($renderingResult->imagePath);

            $task = $this->api->requestTask();
        }

        $this->logger->addInfo('Can\'t get more tasks. Exiting');
    }

    /**
     * @param Api $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * @param FileRepository $fileRepository
     */
    public function setFileRepository($fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Blender $blender
     */
    public function setBlender($blender)
    {
        $this->blender = $blender;
    }
}