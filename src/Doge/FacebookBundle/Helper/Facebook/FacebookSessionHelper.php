<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 08/04/15
 * Time: 20:01
 */

namespace Doge\FacebookBundle\Helper\Facebook;

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class FacebookSessionHelper{

    /**
     * @var FacebookRedirectLoginHelper
     */
    protected $helper;

    /**
     * @var FacebookSession
     */
    protected $session = null;

    /**
     * @var GraphUser
     */
    protected $user = null;

    /**
     * @param Request $request
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct( Request $request, Router $router, $appId, $appSecret )
    {

        FacebookSession::setDefaultApplication( $appId, $appSecret );
        $this->helper = new FacebookRedirectLoginHelper( $router->generate( "doge_facebook_home", [], Router::ABSOLUTE_URL ) );
    }

    /**
     * Gets the current Facebook Session
     *
     */
    protected function handleSession(){
        $this->session = $this->helper->getSessionFromRedirect();

        if( !$this->session && $_SESSION && isset( $_SESSION['fbToken'] ) ){
            $this->session = new FacebookSession( $_SESSION['fbToken'] );
            $_SESSION['fbToken'] = $this->session->getAccessToken();

            return true;
        } else if ( $this->session ){
            return true;
        }

        return false;
    }

    /**
     * Destroys the current Facebook Session
     */
    public function destroySession()
    {
        if( $this->session ){
            $this->session = null;
        }

        if( $_SESSION && $_SESSION['fbToken'] ){
            unset( $_SESSION['fbToken'] );
        }
    }

    /**
     * Initiates a Facebook User from the current session;
     */
    public function handleUser()
    {
        if( !$this->session ){
            $this->handleSession();
        }

        $request = new FacebookRequest( $this->session, "GET", "/me");
        $this->user = $request->execute()->getGraphObject( GraphUser::className() );
    }

    /**
     * @return GraphUser
     */
    public function getUser()
    {
        if( !$this->user ){
            $this->handleUser();
        }

        return $this->user;
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