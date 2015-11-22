<?php

namespace MoSchulze\BlenderFarmClient;

class FileRepository
{
    private $fileBasePath = '';

    public function hasFreshestProjectFile($projectId, $md5)
    {
        $filePath = $this->fileBasePath . $projectId . '/project.blend';
        if(!file_exists($filePath)) {
            return false;
        }

        if(md5(file_get_contents($filePath)) !== $md5) {
            return false;
        }

        return true;
    }

    public function getProjectFilePath($projectId)
    {
        return $this->fileBasePath . $projectId . '/project.blend';
    }

    public function putProjectFile($projectId, $tmpFilePath)
    {
        $folderPath = $this->fileBasePath . $projectId;
        $filePath = $folderPath . '/project.blend';
        if(!file_exists($folderPath)) {
            mkdir($folderPath);
        } elseif(file_exists($filePath)) {
            unlink($filePath);
        }
        rename($tmpFilePath, $filePath);
    }

    /**
     * @param string $fileBasePath
     */
    public function setFileBasePath($fileBasePath)
    {
        $this->fileBasePath = $fileBasePath;
    }
}