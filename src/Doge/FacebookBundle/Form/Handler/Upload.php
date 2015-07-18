<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 16/05/15
 * Time: 11:37
 */

namespace Doge\FacebookBundle\Form\Handler\Upload;

use Doctrine\ORM\EntityManager;
use Doge\FacebookBundle\Entity\Image;
use Doge\FacebookBundle\Helper\Facebook\RequestFacebook;
use Facebook\FacebookRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Upload {
    /**
     * @var RequestFacebook
     */
    protected $fbRequest;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @param RequestFacebook $requestFacebook
     * @param EntityManager $em
     * @param Request $request
     */
    public function __construct( RequestFacebook $requestFacebook, EntityManager $em, Request $request, TokenStorage $tokenStorage ){
        $this->fbRequest = $requestFacebook;
        $this->em = $em;
        $this->request = $request;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return string
     * @throws FacebookRequestException
     * @throws \Exception
     */
    public function handleRequest()
    {
        $image = $this->em->getRepository("DogeFacebookBundle:Image")->findOneBy(["userId" => $this->tokenStorage->getToken()->getUser()->getId()]);

        if( !$image ){
            $image = new Image();
        }

        echo "<pre>"; var_dump($_POST); echo "</pre>"; 
        echo "<pre>"; var_dump($this->request->get("choix_image", false)); echo "</pre>";die;
        if( $id = $this->request->get("choix_image", false) ){
            $image->setPostId( $id );

            $this->em->persist($image);
            $this->em->flush();

            return "L'image choisie est maintenant utilisée pour le concours !";
        } else {
            if( !is_dir( $this->getImageDir() ) ){
                mkdir( $this->getImageDir(), 0764, true );
            }

            if( isset( $_FILES['form']['tmp_name']['file'] ) ){
                $file = $this->getImageDir() . DIRECTORY_SEPARATOR . $_FILES['form']['name']['file'];
                move_uploaded_file( $_FILES['form']['tmp_name']['file'], $file  );
            }

            try {
                $request = $this->fbRequest->postPhoto( $file );
                $image->setPostId( $request->getProperty("id") );
                $image->setUserId( $this->tokenStorage->getToken()->getUser()->getId() );
                $this->em->persist($image);
                $this->em->flush();

                // If you're not using PHP 5.5 or later, change the file reference to:
                // 'source' => '@/path/to/file.name'
                return "L'image " . $_FILES['form']['name']['file'] . " a été téléchargée sur Facebook avec succès !";

            } catch(\Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * @return string
     */
    protected function getImageDir(){
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return 'images'.DIRECTORY_SEPARATOR.'posted_images';
    }
}