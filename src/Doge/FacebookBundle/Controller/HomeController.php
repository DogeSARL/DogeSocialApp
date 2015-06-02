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

    public function slideshowAction(){
    	return $this->render("DogeFacebookBundle:Home:slideshow.html.twig");
    }

    public function participeAction()
    {
        return $this->render("DogeFacebookBundle:Home:participe.html.twig");
    }

    public function galleryAction(){
    	return $this->render("DogeFacebookBundle:Home:gallery.html.twig");
    }

}

