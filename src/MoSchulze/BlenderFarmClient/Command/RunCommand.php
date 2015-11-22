<?php

namespace MoSchulze\BlenderFarmClient\Command;

use MoSchulze\BlenderFarmClient\Client;
use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    /**
     * @var Client
     */
    private $client;

    protected function configure()
    {
        $this->setName('run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->client->run();
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}