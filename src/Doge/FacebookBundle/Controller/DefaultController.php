<?php

namespace Doge\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DogeFacebookBundle:Default:index.html.twig', array('name' => $name));
    }
}
