<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Command;

use Doctrine\ORM\EntityManager;
use Fieg\FanControl\Entity\TempReading;
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

        $container = $kernel->getContainer();
        $loop = $kernel->getLoop();

        $loop->addPeriodicTimer(
            5,
            function () {
                $kmem = memory_get_usage(true) / 1024;
                echo "Memory: $kmem KiB\n";
            }
        );

        /** @var EntityManager $em */
        $em = $container->get('doctrine.entity_manager');

        $port = $input->getArgument('port');

        $client = new Client($loop);
        $client->listen($port);

        $client->on('line', function($data, $client) use ($em) {
                $temp = floatval($data);

                $reading = new TempReading();
                $reading->setTemp($temp);
                $reading->setDatetimeReading(new \DateTime());

                $em->persist($reading);
                $em->flush($reading);
            }
        );

        $loop->run();
    }
}
