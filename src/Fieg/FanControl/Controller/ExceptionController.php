<?php

namespace Fieg\FanControl\Controller;

use Doctrine\ORM\EntityManager;
use Fieg\FanControl\Entity\TempReading;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExceptionController extends Controller
{
    public function errorAction($exception)
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        return new Response(
            $twig->render('view/Exception/error.html.twig', array('exception' => $exception))
        );
    }
}


