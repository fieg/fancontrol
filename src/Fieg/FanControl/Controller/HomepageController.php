<?php

namespace Fieg\FanControl\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends Controller
{
    /**
     * @Route("/")
     *
     * @return string
     */
    public function indexAction()
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        return new Response(
            $twig->render('view/Homepage/index.html.twig', array())
        );
    }
}


