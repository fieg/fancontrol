<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Command;

use Doctrine\ORM\EntityManager;
use Fieg\FanControl\Entity\TempReading;
use Fieg\FanControl\Kernel\Kernel;
use Fieg\FanControl\Serial\Client;
use React\Http\Request;
use React\Http\Response;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;

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
        /** @var Kernel $kernel */
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

        /** @var AnnotationDirectoryLoader $annotationDirectoryLoader */
        $annotationDirectoryLoader = $container->get('AnnotationDirectoryLoader');

        // loads routes
        $container->set('route_collection', $annotationDirectoryLoader->load($container->getParameter('kernel.root_dir') . '/../Controller'));

        /** @var HttpKernel $app */
        $webapp = $container->get('http.kernel');

        $socket = new \React\Socket\Server($loop);
        $http = new \React\Http\Server($socket);

        $http->on(
            'request',
            function(Request $request, Response $response) use ($webapp) {
                printf("[%s] %s %s\n", /*$response->getConnection()->getRemoteAddress()*/ '?.?.?.?', $request->getMethod(), $request->getPath());
                $httpRequest = \Symfony\Component\HttpFoundation\Request::create(
                    $request->getPath(),
                    $request->getMethod(),
                    $request->getQuery()
                );

                $httpResponse = $webapp->handle($httpRequest);

                $headers = $httpResponse->headers->all();
                $headers = array_map('array_shift', $headers);

                $response->writeHead($httpResponse->getStatusCode(), $headers);

                $response->end($httpResponse->getContent());
            }
        );

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
