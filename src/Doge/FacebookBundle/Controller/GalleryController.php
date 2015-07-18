<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 16/05/15
 * Time: 11:51
 */

namespace Doge\FacebookBundle\Controller;


use Facebook\FacebookAuthorizationException;
use Facebook\FacebookRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GalleryController extends Controller {
    public function uploadPhotoAction( Request $request ) {
        $fbRequest = $this->get( "doge.request_facebook" );
        $error = "";

        if ( !$fbRequest->checkPermission( "publish_actions" ) || !$fbRequest->checkPermission( "user_photos" ) ) {
            $error = $this->get( "doge.helper.controller.permission_request" )->reAskPermission( $request );

            if ( is_object( $error ) ) {
                return $error;
            }
        }

        $message = "";

        if ( $request->getMethod() == "POST" && $this->getUser() ) {
            echo "<pre>"; var_dump("ok"); echo "</pre>";
            try {
                $message = $this->get( "doge.form.handler.upload" )->handleRequest();
            } catch ( \Exception $e ) {
                $message = "Une erreur est survenue lors de l'envoi du fichier.";
            }
        }

        $retrievedAlbums = $fbRequest->getUserAlbums()->asArray()['data'];
        $albums = [];

        foreach ( $retrievedAlbums as $album ) {
            $albums[ $album->id ] = $album->name;
        }

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add( "album", "choice", [ 'choices' => $albums ] )
            ->add( "albumName", "text", [ "required" => false ] )
            ->add( "file", "file" )
            ->add( "text", "text", [ "label" => "Describe your photo" ] )
            ->add( "envoyer", "submit", [ "label" => "Send" ] );

        $form = $formBuilder->getForm();

        $formExistingPhotoBuilder = $this->createFormBuilder();
        $formExistingPhotoBuilder->add( "album", "choice", [ 'choices' => $albums ] );
        $formExistingPhoto = $formExistingPhotoBuilder->getForm();

        return $this->render( "DogeFacebookBundle:Gallery:upload.html.twig", [ 'formPhoto' => $formExistingPhoto->createView(), 'form' => $form->createView(), "message" => $message, "error" => $error ] );
    }

    public function galleryAction() {
        $imagesDb = $this->getDoctrine()->getRepository( "DogeFacebookBundle:Image" )->findAll();

        $images = [ ];

        foreach ( $imagesDb as $db ) {
            try {
                $response = ( new FacebookRequest(
                    $this->get( "doge.facebook_session" ), 'GET', '/' . $db->getPostId()
                ) )->execute()->getGraphObject();

                $images[] = [ "url"  => $response->getProperty( "source" ),
                              "name" => $response->getProperty( "name" ),
                              "user" => $response->getProperty( "from" )->getProperty( "name" ),
                              "id"   => $db->getPostId(),
                ];

            } catch ( FacebookAuthorizationException $e ) {
            }
        }

        return $this->render( "DogeFacebookBundle:Gallery:gallery.html.twig", [ "images" => $images ] );
    }

    public function getPhotosFromAlbumIdAction( $id ) {
        $facebookRequestHelper = $this->get( "doge.request_facebook" );

        $images = [ ];


        foreach ( $facebookRequestHelper->getAlbumPhotos( $id )->asArray()['data'] as $photo ) {
            $image = $photo->images[0];

            foreach( $photo->images as $anotherImage ){
                if( $anotherImage->height > 443 && abs( $anotherImage->height ) < abs( $image->height ) ){
                    $image = clone( $anotherImage );
                }
            }

            $images[] = [
                "id"   => $photo->id,
                "link" => $image->source,
                "name" => $photo->name
            ];
        }

        return new JsonResponse( $images );
    }
}