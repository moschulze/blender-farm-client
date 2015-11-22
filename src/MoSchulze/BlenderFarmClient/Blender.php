<?php

namespace MoSchulze\BlenderFarmClient;

class Blender
{
    private $pathToBlender = 'blender';

    private $frameNumberLength = 5;

    private $imageFormats = array();

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
        $command .= ' -E CYCLES';
        $command .= ' -F ' . $task->format;
        $command .= ' -o ' . $outputFilePattern;
        $command .= ' -f ' . $task->frameNumber;
        exec($command);

        $fileExtension = $this->imageFormats[$task->format];
        $imagePath = $projectDirectory . sprintf("frame_%'.0" . $this->frameNumberLength . "d", $task->frameNumber) . '.' . $fileExtension;

        return $imagePath;
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
}