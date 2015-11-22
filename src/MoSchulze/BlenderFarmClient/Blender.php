<?php

namespace MoSchulze\BlenderFarmClient;

class Blender
{
    private $pathToBlender = 'blender';

    /**
     * @var FileRepository
     */
    private $fileRepository;

    public function renderFrame(Task $task)
    {

        $command  = $this->pathToBlender . ' -b ';
        $command .= $this->fileRepository->getProjectFilePath($task->projectId);
        $command .= ' -E CYCLES';
        $command .= ' -F PNG';
        $command .= ' -o ' . $this->fileRepository->getProjectDirectory($task->projectId) . 'frame#####';
        $command .= ' -f ' . $task->frameNumber;
        exec($command);

        $imagePath = $this->fileRepository->getProjectDirectory($task->projectId) . sprintf("frame%'.05d", $task->frameNumber);

        return $imagePath . '.png';
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
}