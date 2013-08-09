<?php

namespace Fieg\FanControl\Controller;

class HomepageController extends Controller
{
    public function indexAction()
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        return $twig->render('view/Homepage/index.html.twig', array());
    }
}


