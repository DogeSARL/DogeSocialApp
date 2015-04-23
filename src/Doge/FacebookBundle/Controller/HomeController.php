<?php

namespace Doge\FacebookBundle\Controller;

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function newAction() {
        return $this->render('DogeFacebookBundle:Home:new.html.twig');
    }

    public function slideshowAction()
    {
        return $this->render("DogeFacebookBundle:Home:slideshow.html.twig");
    }

    public function uploadPhotoAction( Request $request )
    {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add("file", "file")->add("envoyer", "submit");

        $form = $formBuilder->getForm();
        $message = "";

        echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump($this->getUser()); echo "</pre>";die;
        echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump($request->getMethod() == "POST" && $this->getUser()); echo "</pre>";die;
        if( $request->getMethod() == "POST" && $this->getUser() ){
            $form->handleRequest( $request );

            if( !is_dir( $this->getImageDir() ) ){
                mkdir( $this->getImageDir(), 0764, true );
            }

            $file = $form->get("file")->getData();

            $extension = $file->guessExtension();

            $fileName = explode( ".", $file->getClientOriginalName() );
            array_pop( $fileName );
            $fileName = implode( $fileName, "." );

            $form->get("file")->getData()->move( $this->getImageDir(), "1_" . $fileName . '.' . $extension );

            try {
                // Upload to a user's profile. The photo will be in the
                // first album in the profile. You can also upload to
                // a specific album by using /ALBUM_ID as the path
                $response = (new FacebookRequest(
                    $this->getUser()->getFacebookAccessToken(), 'POST', '/me/photos', array(
                        'source' => new \CURLFile( $this->getImageDir() . DIRECTORY_SEPARATOR . $fileName, $file->getMimeType() ),
                        'message' => 'A photo'
                    )
                ))->execute()->getGraphObject();

                die;
                // If you're not using PHP 5.5 or later, change the file reference to:
                // 'source' => '@/path/to/file.name'
                $message = "ok";

            } catch(FacebookRequestException $e) {
                $message = "error";
            }
        }

        return $this->render("DogeFacebookBundle:Home:upload.html.twig", [ 'form' => $form->createView(), "message" => $message ]);
    }

    protected function getImageDir(){
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'images'.DIRECTORY_SEPARATOR.'posted_images';
    }
}

