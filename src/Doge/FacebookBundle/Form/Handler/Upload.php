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

class Upload {

    protected $fbRequest;

    protected $em;

    public function __construct( RequestFacebook $requestFacebook, EntityManager $em ){
        $this->fbRequest = $requestFacebook;
        $this->em = $em;
    }

    public function handleRequest()
    {
        if( !is_dir( $this->getImageDir() ) ){
            mkdir( $this->getImageDir(), 0764, true );
        }

        if( isset( $_FILES['form']['tmp_name']['file'] ) ){
            $file = $this->getImageDir() . DIRECTORY_SEPARATOR . $_FILES['form']['name']['file'];
            move_uploaded_file( $_FILES['form']['tmp_name']['file'], $file  );
        }

        try {
            $request = $this->fbRequest->postPhoto( $file );

            $image = new Image();
            $image->setPostId( $request->getProperty("id") );

            $this->em->persist($image);
            $this->em->flush();

            // If you're not using PHP 5.5 or later, change the file reference to:
            // 'source' => '@/path/to/file.name'
            return "L'image " . $_FILES['form']['name']['file'] . "a été téléchargée sur Facebook avec succès !";

        } catch(FacebookRequestException $e) {
            throw $e;
        }
    }

    protected function getImageDir(){
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'images'.DIRECTORY_SEPARATOR.'posted_images';
    }
}