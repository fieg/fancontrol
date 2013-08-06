<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Command;

use Fieg\FanControl\Serial\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('server')
            ->setDescription('Start FanControl server')
            ->addArgument('port', InputArgument::OPTIONAL, 'Serial port', "/dev/cu.usbmodem411")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getApplication()->getKernel();

        $loop = $kernel->getLoop();

        $port = $input->getArgument('port');

        $client = new Client($loop);
        $client->listen($port);

        $client->on('line', function($data, $client) {
                var_dump($data);
            }
        );

        $loop->run();
    }
}
