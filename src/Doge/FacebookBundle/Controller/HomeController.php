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
    public function newAction() {
        return $this->render('DogeFacebookBundle:Home:new.html.twig');
    }

    public function slideshowAction()
    {
        return $this->render("DogeFacebookBundle:Home:slideshow.html.twig");
    }

    public function uploadPhotoAction( Request $request )
    {
        $fbRequest = $this->get("doge.request_facebook");
        $serror = "";

        echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump($fbRequest->checkPermission("publish_actions")); echo "</pre>";
        if( !$fbRequest->checkPermission("publish_actions") ){
            $alreadyAsked = $this->get("session")->getFlashBag()->get("asking_permission");

            echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump($alreadyAsked); echo "</pre>";

            if( $alreadyAsked ){
                $error = "Kawaii Pets n'a pas le droit de poster du contenu sur votre compte.";
            } else {
                echo "ok";
                $this->addFlash("asking_permission", true);
                echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump($this->generateUrl( $request->get("_route"), [], UrlGeneratorInterface::ABSOLUTE_PATH )); echo "</pre>";die;

                return $this->redirect(
                    $this->container->get('hwi_oauth.security.oauth_utils')->getAuthorizationUrl(
                        $request,
                        "facebook",
                        $this->generateUrl( $request->get("_route"), [], UrlGeneratorInterface::ABSOLUTE_PATH ),
                        [ "auth_type" => "rerequest" ]
                    )
                );
            }
        }

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add("file", "file")->add("text", "text")->add("envoyer", "submit");

        $form = $formBuilder->getForm();
        $message = "";

        if( $request->getMethod() == "POST" && $this->getUser() ){
            if( !is_dir( $this->getImageDir() ) ){
                mkdir( $this->getImageDir(), 0764, true );
            }

            if( isset( $_FILES['form']['tmp_name']['file'] ) ){
                $file = $this->getImageDir() . DIRECTORY_SEPARATOR . $_FILES['form']['name']['file'];
                move_uploaded_file( $_FILES['form']['tmp_name']['file'], $file  );
            }

            try {
                $request = $fbRequest->postPhoto( $file, $_POST['form']['file']);

                $image = new Image();
                $image->setPostId( $request->getProperty("id") );

                $this->getDoctrine()->getManager()->persist($image);
                $this->getDoctrine()->getManager()->flush();

                // If you're not using PHP 5.5 or later, change the file reference to:
                // 'source' => '@/path/to/file.name'
                $message = "L'image " . $_FILES['form']['name']['file'] . "a été téléchargée sur Facebook avec succès !";

            } catch(FacebookRequestException $e) {

            }
        }

        return $this->render("DogeFacebookBundle:Home:upload.html.twig", [ 'form' => $form->createView(), "message" => $message ]);
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

        return $this->render("DogeFacebookBundle:Home:gallery.html.twig", ["images" => $images]);
    }

    protected function getImageDir(){
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'images'.DIRECTORY_SEPARATOR.'posted_images';
    }
}

