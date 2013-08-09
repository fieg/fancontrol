<?php

namespace Fieg\FanControl\Controller;

abstract class Controller
{
    protected $container;

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }
}



