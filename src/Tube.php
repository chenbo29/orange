<?php


namespace orange;


use Pheanstalk\Pheanstalk;
use Pimple\Container;

class Tube
{
    private $name;
    private $container;
    private $pheanstalk;

    public function __construct(Container $container)
    {
        $this->container  = $container;
        $this->pheanstalk = $container['pheanstalk'];
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPheanstalk(): Pheanstalk
    {
        return $this->pheanstalk;
    }

    public function status()
    {
        $stats = $this->getPheanstalk()->useTube($this->name)->stats();
        $properties = ['pid', 'cmd_put', 'cmd_stats_job'];
        $data = [];
        array_walk($properties, function ($v) use ($stats, &$data) {
            array_push($data, $stats->$v);
        });
        return $data;
    }

    public function create() {
        return $this->getPheanstalk()->putInTube($this->name, json_encode(['test' => 'chenbo', 'time' => date('Y-m-d H:i:s')], true));
    }
}