<?php

namespace Fieg\FanControl\Controller;

use Doctrine\ORM\EntityManager;
use Fieg\FanControl\Entity\TempReading;

class DataController extends Controller
{
    public function indexAction()
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.entity_manager');

        $qb = $em->getRepository('Fieg\Fancontrol\Entity\TempReading')->createQueryBuilder('r');

        $qb
            ->select()
            ->orderBy('r.datetimeReading', 'DESC')
            ->setMaxResults(100)
        ;

        /** @var TempReading[] $readings */
        $readings = $qb->getQuery()->getResult();

        return $twig->render('view/Data/index.json.twig', array('readings' => $readings));
    }
}


