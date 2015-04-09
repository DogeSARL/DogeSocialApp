<?php

namespace Doge\FacebookBundle\Controller;

use Doge\FacebookBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $user = new User();
        $user->setAge(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist( $user );
        $em->flush();

        $em->remove( $user );

        return $this->render('DogeFacebookBundle:Default:index.html.twig', array('name' => $name));
    }
}
