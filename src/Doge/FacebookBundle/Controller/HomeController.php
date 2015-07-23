<?php

namespace Doge\FacebookBundle\Controller;

use Doge\FacebookBundle\Entity\Image;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends Controller
{
	public function newAction(){
		return $this->render('DogeFacebookBundle:Home:new.html.twig');
	}

    public function participeAction()
    {
        return $this->render("DogeFacebookBundle:Home:participe.html.twig");
    }

    public function galleryAction(){
    	return $this->render("DogeFacebookBundle:Home:gallery.html.twig");
    }

    public function termsAction(){
        return $this->render("DogeFacebookBundle:Home:tos.html.twig");
    }

    public function rulesAction(){
        return $this->render("DogeFacebookBundle:Home:rules.html.twig");
    }
}

