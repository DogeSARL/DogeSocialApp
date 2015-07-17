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
        $facebookRequestHelper = $this->get( "doge.request_facebook" );

        $images = [ ];


        foreach ( $facebookRequestHelper->getAlbumPhotos( 1491070590287 )->asArray()['data'] as $photo ) {
            $image = $photo->images[0];

            foreach( $photo->images as $anotherImage ){
                if( $anotherImage->width > 250 && abs( $anotherImage->width ) < abs( $image->width ) ){
                    $image = clone( $anotherImage );
                }
            }

            $images[] = [
                "id"   => $photo->id,
                "link" => $image->source,
                "name" => $photo->name
            ];
        }

        die;

        return $this->render("DogeFacebookBundle:Default:test.html.twig", [ "loginUrl" => $loginUrl ] );
    }
}
