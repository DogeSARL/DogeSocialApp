<?php

namespace Doge\FacebookBundle\Facebook;

use Facebook\FacebookSession as BaseSession;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class FacebookSession extends BaseSession {

    public function __construct( TokenStorage $tokenStorage )
    {
        if( null != $tokenStorage->getToken() ){
            $user = $tokenStorage->getToken()->getUser();

            if( $user->getAccessToken() != null ){
                parent::__construct( $user->getAccessToken() );
            }
        }

        echo "\n<pre>"; \Doctrine\Common\Util\Debug::dump(parent::getAccessToken()); echo "</pre>";
    }
}