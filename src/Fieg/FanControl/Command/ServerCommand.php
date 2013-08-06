<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Command;

use Fieg\FanControl\Serial\Client;
use Fieg\FanControl\Serial\Server;
use React\Stream\Stream;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getApplication()->getKernel();

        $loop = $kernel->getLoop();

        $client = new Client($loop);
        $client->listen('/dev/cu.usbmodem411');

        $client->on('line', function($data, $client) {
                var_dump($data);
            }
        );

        $loop->run();
    }
}
