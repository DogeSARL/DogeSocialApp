<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 15/05/15
 * Time: 15:41
 */

namespace Doge\FacebookBundle\Helper\Facebook;


use Doge\FacebookBundle\Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;

class RequestFacebook {
    protected $fbSession;

    public function __construct(FacebookSession $facebookSession)
    {
        $this->fbSession = $facebookSession;
    }

    /**
     * @param $file
     * @param $text
     * @return GraphObject
     * @throws \Facebook\FacebookRequestException
     */
    public function postPhoto( $file, $text ){
        // Upload to a user's profile. The photo will be in the
        // first album in the profile. You can also upload to
        // a specific album by using /ALBUM_ID as the path
        $response = (new FacebookRequest(
            $this->fbSession, 'POST', '/me/photos', array(
                'source' => new \CURLFile( $file ),
                'message' => $text
            )
        ))->execute();

        return $response->getGraphObject();
    }

    /**
     * @param $permission
     * @return boolean
     * @throws \Facebook\FacebookRequestException
     */
    public function checkPermission( $permissionLabel ){
        $response = (new FacebookRequest(
            $this->fbSession, 'GET', '/me/permissions'
        ))->execute()->getGraphObject();

        foreach( $response->asArray() as $permission ){
            if( $permission->permission == $permissionLabel && $permission->status == "granted" ){
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     * @throws \Facebook\FacebookRequestException
     */
    public function getUserAlbums()
    {
        $response = (new FacebookRequest(
           $this->fbSession, 'GET', '/me/albums'
        ))->execute();

        return $response->getGraphObject()['data'];
    }
}