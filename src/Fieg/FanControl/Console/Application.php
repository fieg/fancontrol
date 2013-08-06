<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Console;

use Fieg\FanControl\Command\ServerCommand;
use Fieg\FanControl\Kernel\Kernel;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct('FanControl', 1.0);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->kernel->boot();

        return parent::doRun($input, $output);
    }

    protected function getDefaultCommands()
    {
        return array_merge(
            parent::getDefaultCommands(),
            array(
                new ServerCommand(),
            )
        );
    }

    /**
     * @return \Fieg\FanControl\Kernel\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }
}
