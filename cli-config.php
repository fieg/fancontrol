<?php

set_time_limit(0);

use Fieg\FanControl\Kernel\Kernel;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$kernel = new Kernel();
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine.entity_manager');

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));
