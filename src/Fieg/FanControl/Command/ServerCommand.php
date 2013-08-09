<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Command;

use Doctrine\ORM\EntityManager;
use Fieg\FanControl\Controller\DataController;
use Fieg\FanControl\Controller\HomepageController;
use Fieg\FanControl\Entity\TempReading;
use Fieg\FanControl\Serial\Client;
use React\Http\Request;
use React\Http\Response;
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

        /** @var EntityManager $em */
        $em = $container->get('doctrine.entity_manager');

        $loop->addPeriodicTimer(
            5,
            function () {
                $kmem = memory_get_usage(true) / 1024;
                echo "Memory: $kmem KiB\n";
            }
        );

        $app = function(Request $request, Response $response) use ($container) {
            switch ($request->getPath()) {
                case '/':
                    $controller = new HomepageController();
                    $controller->setContainer($container);

                    $output = $controller->indexAction();
                    $response->writeHead(200, array('Content-Type' => 'text/html'));
                    $response->end($output);

                    break;
                case '/data.json':
                    $controller = new DataController();
                    $controller->setContainer($container);

                    $output = $controller->indexAction();
                    $response->writeHead(200, array('Content-Type' => 'application/json'));
                    $response->end($output);

                    break;
                default:
                    $response->writeHead(404, array('Content-Type' => 'text/text'));
                    $response->end(sprintf("Not found\nThe path %s was not found", $request->getPath()));

                    break;
            }

        };

        $socket = new \React\Socket\Server($loop);
        $http = new \React\Http\Server($socket);

        $http->on('request', $app);

        $socket->listen(1337, '0.0.0.0');

        $port = $input->getArgument('port');

        $client = new Client($loop);

        try {
            $client->listen($port);
        } catch (\Exception $e) {
            printf("%s\n", $e->getMessage());
        }

        $client->on('line', function($data, $client) use ($em) {
                if (!is_numeric($data)) {
                    printf("%s\n", $data);

                    return;
                }

                $temp = floatval($data);

                printf("%.2f\n", $temp);

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
