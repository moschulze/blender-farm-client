<?php

namespace MoSchulze\BlenderFarmClient;

class Report
{
    /**
     * @var Task
     */
    public $task;

    /**
     * @var double
     */
    public $runtime;

    /**
     * @var double
     */
    public $remaining;

    /**
     * @var double
     */
    public $progress;
}