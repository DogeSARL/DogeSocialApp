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
        $photos = $this->getDoctrine()->getManager()->getRepository("DogeFacebookBundle:Image")->findAll();

        $likeCount = 0;
        $winner = null;
        $facebookRequestHelper = $this->get("doge.request_facebook");

        foreach( $photos as $photo ){
            $photoLikeCount = $facebookRequestHelper->getLikeCount( $photo->getPostId() );

            if( $photoLikeCount > $likeCount || ( $photoLikeCount == $likeCount && $likeCount == 0 ) ){
                $winner = $photo;
                $likeCount = $photoLikeCount;
            }

            $winner = $facebookRequestHelper->getPhoto( $winner->getPostId() );
            
            echo "\n<pre>"; var_dump($winner); echo "</pre>";
        }

		return $this->render('DogeFacebookBundle:Home:new.html.twig', [ "winner" => $winner ]);
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

