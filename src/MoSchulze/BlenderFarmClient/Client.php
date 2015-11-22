<?php

namespace MoSchulze\BlenderFarmClient;

use Monolog\Logger;

class Client
{
    private $apiUrl = '';

    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var Logger
     */
    private $logger;

    public function run()
    {
        $task = $this->requestTask();

        while(!is_null($task)) {
            $this->logger->addInfo('project ' . $task->projectId . ', frame ' . $task->frameNumber);

            if(!$this->fileRepository->hasFreshestProjectFile($task->projectId, $task->projectMd5)) {
                $this->logger->addInfo('downloading file for project ' . $task->projectId);
                $filePath = $this->requestFile($task->projectId);
                $this->fileRepository->putProjectFile($task->projectId, $filePath);
            }

            $this->logger->addInfo('rendering');
            //ToDo render

            $this->logger->addInfo('uploading image');
            //ToDo upload rendered image

            $task = $this->requestTask();
        }

        $this->logger->addInfo('Can\'t get more tasks. Exiting');
    }

    private function requestTask()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->apiUrl . 'work');
        $data = json_decode($response->getBody(), true);

        if($data['status'] !== 'ok') {
            return null;
        }

        $task = new Task();
        $task->frameNumber = $data['frame'];
        $task->projectId = $data['project'];
        $task->projectMd5 = $data['md5'];

        return $task;
    }

    private function requestFile($projectId)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($this->apiUrl . 'project/' . $projectId);
        $filePath = '/tmp/'.md5(rand().microtime());
        file_put_contents($filePath, $response->getBody());
        return $filePath;
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
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
}