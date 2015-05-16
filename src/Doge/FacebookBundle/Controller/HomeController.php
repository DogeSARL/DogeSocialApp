<?php

namespace Doge\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
	public function newAction(){
		return $this->render('DogeFacebookBundle:Home:new.html.twig');
	}

    public function slideshowAction(){
    	return $this->render("DogeFacebookBundle:Home:slideshow.html.twig");
    }

    public function participeAction(){
    	return $this->render("DogeFacebookBundle:Home:participe.html.twig");
    }

    public function galleryAction(){
    	return $this->render("DogeFacebookBundle:Home:gallery.html.twig");
    }

}

