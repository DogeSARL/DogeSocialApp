<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 08/04/15
 * Time: 20:01
 */

namespace Doge\FacebookBundle\Helper\Facebook;

use Doge\FacebookBundle\Entity\User;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class FacebookSessionHelper{
    /**
     * @var FacebookSession
     */
    protected $session = null;

    /**
     * @var GraphUser
     */
    protected $fbUser = null;

    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage = null;

    /**
     * @param Request $request
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct( Request $request, Router $router, TokenStorage $tokenStorage, $appId, $appSecret )
    {
        FacebookSession::setDefaultApplication( $appId, $appSecret );
        $this->tokenStorage = $tokenStorage;

        $this->user = $this->getUser();

        if( $this->user ){
            $this->session = new FacebookSession( $this->user->getAccessToken );
        }

    }

    /**
     * Gets the current Facebook Session
     *
     */
    protected function handleSession(){
    }

    /**
     * Initiates a Facebook User from the current session;
     */
    public function handleUser()
    {
        $request = new FacebookRequest( $this->session, "GET", "/me");
        $this->user = $request->execute()->getGraphObject( GraphUser::className() );
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->tokenStorage) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    /**
     * @return FacebookSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return FacebookRedirectLoginHelper
     */
    public function getFacebookHelper()
    {
        return $this->helper;
    }

    /**
     * @return bool
     */
    public function isUserConnected()
    {
        return ( $this->session || $_SESSION && isset( $_SESSION['fbToken'] ) );
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->helper->getLoginUrl();
    }
}