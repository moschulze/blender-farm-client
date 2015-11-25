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
        $task->projectId = $data['project'];
        $task->projectMd5 = $data['md5'];
        $task->id = $data['id'];
        $task->format = $data['format'];
        $task->engine = $data['engine'];

        return $task;
    }

    /**
     * @param Task $task
     * @return string path to tmp file
     */
    public function requestProjectFileForTask(Task $task)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($this->apiUrl . 'project/' . $task->projectId);
        $filePath = '/tmp/'.md5(rand().microtime());
        file_put_contents($filePath, $response->getBody());
        return $filePath;
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