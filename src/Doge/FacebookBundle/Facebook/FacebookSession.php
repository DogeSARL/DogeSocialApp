<?php

namespace Doge\FacebookBundle\Facebook;

use Facebook\FacebookSession as BaseSession;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class FacebookSession extends BaseSession {

    public function __construct( TokenStorage $tokenStorage, $appId, $appSecret )
    {
        FacebookSession::setDefaultApplication($appId, $appSecret);

        if( null != $tokenStorage->getToken() ){
            $user = $tokenStorage->getToken()->getUser();

            if( is_object( $user ) && $user->getFacebookAccessToken() != null ){
                parent::__construct( $user->getFacebookAccessToken() );
            }
        }
    }
}