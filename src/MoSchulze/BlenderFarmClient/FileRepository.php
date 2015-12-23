<?php

namespace MoSchulze\BlenderFarmClient;

class FileRepository
{
    private $fileBasePath = '';

    public function getProjectDirectory($projectId)
    {
        return $this->fileBasePath . $projectId . '/';
    }

    public function deleteAllProjects()
    {
        foreach(glob($this->fileBasePath . '*') as $file) {
            if(is_dir($file)) {
                $this->deleteRecursive($file);
            }
        }
    }

    private function deleteRecursive($directory)
    {
        foreach(glob($directory . '/*') as $file) {
            if(is_dir($file)) {
                $this->deleteRecursive($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directory);
    }

    public function getProjectFilePath($filePath, $projectId)
    {
        return $this->getProjectDirectory($projectId) . $filePath;
    }

    public function getFilesThatNeedUpdate($filesArray, $projectId)
    {
        $result = array();
        $projectDirectory = $this->getProjectDirectory($projectId);
        foreach($filesArray as $file) {
            $path = $projectDirectory . $file['path'];
            if(!file_exists($path) || md5_file($path) != $file['md5']) {
                $result[] = $file;
            }
        }
        return $result;
    }

    public function addDownloadedFileToProject($tmpPath, $realFilePath, $projectId)
    {
        $projectDirectory = $this->getProjectDirectory($projectId);
        $targetPath = $projectDirectory . $realFilePath;

        if(!file_exists(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0777, true);
        }

        rename($tmpPath, $targetPath);
    }

    /**
     * @param string $fileBasePath
     */
    public function setFileBasePath($fileBasePath)
    {
        $this->fileBasePath = rtrim($fileBasePath, '/') . '/';
    }
}