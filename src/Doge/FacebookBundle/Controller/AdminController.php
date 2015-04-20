<?php

namespace Doge\FacebookBundle\Controller;

use Doge\FacebookBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Security;

class AdminController extends Controller
{
  public function indexAction() {

    $url = 'https://api.facebook.com/method/fql.query?query=select%20like_count%20from%20link_stat%20where%20url=%27https://www.facebook.com/pages/Kawaii-Pets/801971733218893?fref=ts/%27&format=json';
    $obj = json_decode(file_get_contents($url), true);
    return $this->render('DogeFacebookBundle:Admin:index.html.twig', ['json' => $obj]);
  }
}

