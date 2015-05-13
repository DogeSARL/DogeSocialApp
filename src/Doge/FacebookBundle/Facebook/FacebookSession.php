<?php

namespace Doge\FacebookBundle\Facebook;

use Facebook\FacebookSession as BaseSession;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class FacebookSession extends BaseSession {

    public function __construct( TokenStorage $tokenStorage )
    {
        if( null != $tokenStorage->getToken() ){
            $user = $tokenStorage->getToken()->getUser();

            echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump($user); echo "</pre>";

            if( is_object( $user ) && $user->getFacebookAccessToken() != null ){
                parent::__construct( $user->getFacebookAccessToken() );
            }
        }

        echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump(parent::getAccessToken()); echo "</pre>";
    }
}