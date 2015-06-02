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
use Facebook\FacebookRequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GalleryController extends Controller{
    public function uploadPhotoAction( Request $request )
    {
        $fbRequest = $this->get("doge.request_facebook");
        $error = "";

        if( !$fbRequest->checkPermission("publish_actions") && !$fbRequest->checkPermission("user_photos") ){
            $error = $this->get("doge.helper.controller.permission_request")->reAskPermission( $request );

            if( is_object( $error ) ){
                return $error;
            }
        }

        $retrievedAlbums = $fbRequest->getUserAlbums();
        $albums = [];

        foreach( $retrievedAlbums->asArray() as $album ){
            $albums[] = [ "id" => $album->id, "name" => $album->name ];
        }

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add("album", "choice", [ 'choices' => $albums, "empty_value" => "Nouvel album" ] )->add("file", "file")->add("text", "text")->add("envoyer", "submit");

        $form = $formBuilder->getForm();
        $message = "";

        if( $request->getMethod() == "POST" && $this->getUser() ){
            try{
                $this->get("doge.form.handler.upload")->handleRequest();
            } catch( FacebookRequestException $e ){
                $message = "Une erreur est survenue lors de l'envoi du fichier.";
            }
        }

        return $this->render("DogeFacebookBundle:Gallery:upload.html.twig", [ 'form' => $form->createView(), "message" => $message, "error" => $error ]);
    }

    public function galleryAction()
    {
        $imagesDb = $this->getDoctrine()->getRepository("DogeFacebookBundle:Image")->findAll();

        $images = [];

        foreach( $imagesDb as $db ){
            try{
                $response = (new FacebookRequest(
                    $this->get("doge.facebook_session"), 'GET', '/' . $db->getPostId()
                ))->execute()->getGraphObject();

                $images[] = [ "url" => $response->getProperty("source"),
                    "name" => $response->getProperty("name"),
                    "user" => $response->getProperty("from") ];

            } catch( FacebookAuthorizationException $e ){
            }
        }

        return $this->render("DogeFacebookBundle:Gallery:gallery.html.twig", ["images" => $images]);
    }
}