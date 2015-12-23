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
            $this->logger->addInfo('New task: project ' . $task->projectId . ', frame ' . $task->frameNumber);
            $filesThatNeedUpdate = $this->fileRepository->getFilesThatNeedUpdate($task->projectFiles, $task->projectId);

            foreach($filesThatNeedUpdate as $file) {
                $this->logger->addInfo('Downloading file ' . $file['path']);
                $tmpFilePath = $this->api->getFileForProject($file['path'], $task->projectId);
                $this->fileRepository->addDownloadedFileToProject($tmpFilePath, $file['path'], $task->projectId);
            }

            $this->logger->addInfo('Starting render process');
            $renderingResult = $this->blender->renderFrame($task);

            $this->logger->addInfo('Starting image upload');
            $this->api->uploadRenderingResult($renderingResult);

            unlink($renderingResult->imagePath);
            $this->logger->addInfo('Task done');

            $task = $this->api->requestTask();
        }

        $this->fileRepository->deleteAllProjects();
        $this->logger->addInfo('Can\'t get more tasks. Cleaned up and exit');
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