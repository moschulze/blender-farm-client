<?php

namespace MoSchulze\BlenderFarmClient;

class Blender
{
    private $pathToBlender = 'blender';

    private $frameNumberLength = 5;

    private $secondsBetweenReports = 5;

    private $imageFormats = array();

    /**
     * @var Api
     */
    private $api;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    public function renderFrame(Task $task)
    {
        $projectDirectory = $this->fileRepository->getProjectDirectory($task->projectId);
        $outputFilePattern = $projectDirectory . 'frame_' . str_repeat('#', $this->frameNumberLength);

        $command  = $this->pathToBlender . ' -b ';
        $command .= $this->fileRepository->getProjectFilePath($task->projectId);
        $command .= ' -E ' . $task->engine;
        $command .= ' -F ' . $task->format;
        $command .= ' -o ' . $outputFilePattern;
        $command .= ' -f ' . $task->frameNumber;

        $report = new Report();
        $report->task = $task;

        $output = popen($command, 'r');
        $lastReport = time();
        while(!feof($output)) {
            $line = fgets($output);

            $matches = array();
            preg_match(
                '~.* Time:(\d{2}:\d{2}.\d{2}) \| Remaining:(\d{2}:\d{2}.\d{2}).*Path Tracing Tile (\d+)\/(\d+)~',
                $line,
                $matches
            );
            if(empty($matches)) {
                continue;
            }

            $report->progress = $matches[3] / $matches[4];

            $exploded = explode(':', $matches[1]);
            $report->runtime = $exploded[0]*60 + $exploded[1];

            $exploded = explode(':', $matches[2]);
            $report->remaining = $exploded[0]*60 + $exploded[1];

            if(time() - $lastReport >= $this->secondsBetweenReports) {
                $this->api->sendReport($report);
                $lastReport = time();
            }

        }

        $fileExtension = $this->imageFormats[$task->format];
        $imagePath = $projectDirectory . sprintf("frame_%'.0" . $this->frameNumberLength . "d", $task->frameNumber) . '.' . $fileExtension;

        $renderingResult = new RenderingResult();
        $renderingResult->imagePath = $imagePath;
        $renderingResult->runtime = $report->runtime;
        $renderingResult->task = $task;

        return $renderingResult;
    }

    /**
     * @param string $pathToBlender
     */
    public function setPathToBlender($pathToBlender)
    {
        $this->pathToBlender = $pathToBlender;
    }

    /**
     * @param FileRepository $fileRepository
     */
    public function setFileRepository($fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param array $imageFormats
     */
    public function setImageFormats($imageFormats)
    {
        $this->imageFormats = $imageFormats;
    }

    /**
     * @param Api $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }
}