<?php

namespace Doge\FacebookBundle\Controller;

use Doge\FacebookBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Security;

class LoginController extends Controller
{
  public function newAction() {

    $request = $this->getRequest();
    $session = $request->getSession();
    // get the login error if there is one
    if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
        $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
    } else {
        $error = $session->get(Security::AUTHENTICATION_ERROR);
        $session->remove(Security::AUTHENTICATION_ERROR);
    }
    return $this->render('DogeFacebookBundle:Login:new.html.twig', array(
        // last username entered by the user
        'last_username' => $session->get(Security::LAST_USERNAME),
        'error'         => $error,
        'csrf_protection' => false,
    ));
  }
}

