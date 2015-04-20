<?php

namespace Doge\FacebookBundle\Controller;

use Doge\FacebookBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DogeFacebookBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction( Request $request )
    {
        $loginUrl = $this->get("doge_facebook.session.helper")->getLoginUrl();

        return $this->render("DogeFacebookBundle:Default:test.html.twig", [ "loginUrl" => $loginUrl ] );
    }
}
