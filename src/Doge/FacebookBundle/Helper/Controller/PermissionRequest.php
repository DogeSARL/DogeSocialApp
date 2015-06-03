<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 16/05/15
 * Time: 11:44
 */

namespace Doge\FacebookBundle\Helper\Controller;

use HWI\Bundle\OAuthBundle\Security\OAuthUtils;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PermissionRequest {
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var OAuthUtils
     */
    protected $oAuthUtils;

    /**
     * @param Session $session
     * @param Router $router
     * @param OAuthUtils $oAuthUtils
     */
    public function __construct(Session $session, Router $router, OAuthUtils $oAuthUtils )
    {
        $this->session = $session;
        $this->router = $router;
        $this->oAuthUtils = $oAuthUtils;
    }

    /**
     * @param Request $request
     * @return string|RedirectResponse
     */
    public function reAskPermission( Request $request )
    {
        $alreadyAsked = $this->session->getFlashBag()->get("asking_permission");

        if( !empty( $alreadyAsked ) ){
            return "Kawaii Pets n'a pas le droit de poster du contenu sur votre compte.";
        } else {
            $this->session->getFlashBag()->set("asking_permission", true);

            return new RedirectResponse(
                $this->oAuthUtils->getAuthorizationUrl(
                    $request,
                    "facebook",
                    $this->router->generate( $request->get("_route"), [], UrlGeneratorInterface::ABSOLUTE_URL ),
                    [ "auth_type" => "rerequest" ]
                )
            );
        }
    }
}