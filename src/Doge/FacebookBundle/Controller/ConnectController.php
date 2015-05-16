<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 16/05/15
 * Time: 10:33
 */

namespace Doge\FacebookBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ConnectController extends Controller{
    /**
     * @param Request $request
     * @param $service
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToServiceAction(Request $request, $service)
    {
        // Check for a specified target path and store it before redirect if present
        $param = $this->container->getParameter('hwi_oauth.target_path_parameter');

        if ($request->hasSession()) {
            // initialize the session for preventing SessionUnavailableException
            $session = $request->getSession();
            $session->start();

            if (!empty($param) && $targetUrl = $request->get($param, null, true)) {
                $providerKey = $this->container->getParameter('hwi_oauth.firewall_name');
                $request->getSession()->set('_security.' . $providerKey . '.target_path', $targetUrl);
            }
        }

        return $this->redirect($this->container->get('hwi_oauth.security.oauth_utils')->getAuthorizationUrl($request, $service));
    }
}