<?php

namespace MoSchulze\BlenderFarmClient;

class Api
{
    private $apiUrl = '';

    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function requestStatus()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->apiUrl . 'status');
        $data = json_decode($response->getBody(), true);

        if($data['status'] !== 'ok') {
            return false;
        }

        return true;
    }

    /**
     * @return Task|null
     */
    public function requestTask()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->apiUrl . 'work');
        $data = json_decode($response->getBody(), true);

        if($data['status'] !== 'ok') {
            return null;
        }

        $task = new Task();
        $task->frameNumber = $data['frame'];
        $task->projectId = $data['project']['id'];
        $task->projectFiles = $data['project']['files'];
        $task->projectMainFile = $data['project']['mainFile'];
        $task->id = $data['id'];
        $task->format = $data['format'];
        $task->engine = $data['engine'];

        return $task;
    }

    public function getFileForProject($filePath, $projectId)
    {
        $client = new \GuzzleHttp\Client();
        $downloadedFilePath = '/tmp/'.md5(rand().microtime());
        $file = fopen($downloadedFilePath, 'w+');
        $client->request(
            'GET',
            $this->apiUrl . 'file/' . $projectId . '/' . urlencode(base64_encode($filePath)),
            array(
                'sink' => $file
            )
        );

        return $downloadedFilePath;
    }

    /**
     * @param RenderingResult $renderingResult
     */
    public function uploadRenderingResult(RenderingResult $renderingResult)
    {
        $client = new \GuzzleHttp\Client();
        $body = fopen($renderingResult->imagePath, 'r');
        $client->post($this->apiUrl . 'upload/' . $renderingResult->task->id, array(
            'multipart' => array(
                array(
                    'name' => 'runtime',
                    'contents' => (string)$renderingResult->runtime
                ),
                array(
                    'name' => 'file',
                    'contents' => $body
                )
            )
        ));
    }

    /**
     * @param Report $report
     */
    public function sendReport(Report $report)
    {
        $guzzle = new \GuzzleHttp\Client();
        $guzzle->request('POST', $this->apiUrl . 'task/' . $report->task->id . '/report', array(
            'form_params' => array(
                'runtime' => $report->runtime,
                'remaining' => $report->remaining,
                'progress' => $report->progress
            )
        ));
    }
}