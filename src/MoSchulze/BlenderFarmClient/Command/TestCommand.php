<?php

namespace MoSchulze\BlenderFarmClient\Command;

use MoSchulze\BlenderFarmClient\Api;
use MoSchulze\BlenderFarmClient\Blender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    /**
     * @var Api
     */
    private $api;

    /**
     * @var Blender
     */
    private $blender;

    private $filePath;

    private $logPath;

    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Tests the configured setup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Testing API url ... ');
        try {
            $this->api->requestStatus();
            $output->writeln('<info>OK</info>');
        } catch(\Exception $e) {
            $output->writeln('<error>FAIL</error> ');
            $output->writeln('Is the entered URL correct and the server up and running?');
            return;
        }

        $output->write('Testing Blender installation ... ');
        if($this->blender->testInstallation()) {
            $output->writeln('<info>OK</info>');
        } else {
            $output->writeln('<error>FAIL</error>');
            $output->writeln('Is the path to the blender executable correct?');
        }

        $output->write('Testing folder rights for data files ... ');
        if(is_writable($this->filePath)) {
            $output->writeln('<info>OK</info>');
        } else {
            $output->writeln('<error>FAIL</error>');
            $output->writeln('Please set the correct rights for ' . $this->filePath);
        }

        $output->write('Testing folder rights for log files ... ');
        if(is_writable(dirname($this->logPath))) {
            $output->writeln('<info>OK</info>');
        } else {
            $output->writeln('<error>FAIL</error>');
            $output->writeln('Please set the correct rights for ' . dirname($this->logPath));
        }
    }

    /**
     * @param Api $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * @param Blender $blender
     */
    public function setBlender($blender)
    {
        $this->blender = $blender;
    }

    /**
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param mixed $logPath
     */
    public function setLogPath($logPath)
    {
        $this->logPath = $logPath;
    }
}