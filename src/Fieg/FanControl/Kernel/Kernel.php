<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Kernel;

class Kernel 
{
    protected $isBooted = false;

    protected $loop;

    public function boot()
    {
        $this->loop = \React\EventLoop\Factory::create();

        $this->isBooted = true;
    }

    /**
     * @return mixed
     */
    public function getLoop()
    {
        return $this->loop;
    }
}
