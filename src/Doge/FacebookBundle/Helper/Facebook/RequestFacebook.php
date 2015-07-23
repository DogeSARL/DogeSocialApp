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
use Symfony\Component\HttpFoundation\Request;

class RequestFacebook {
    protected $fbSession;

    protected $request;

    public function __construct(FacebookSession $facebookSession, Request $request )
    {
        $this->fbSession = $facebookSession;
        $this->request = $request;
    }

    /**
     * @param $file
     * @param $text
     * @return GraphObject
     * @throws \Facebook\FacebookRequestException
     */
    public function postPhoto( $file ){
        $form = $_POST['form'];
        $uploadOptions = [ 'source' => new \CURLFile( $file ) ];

        if( !empty( $form['text'] ) ){
            $uploadOptions["message"] = $_POST['form']['text'];
        }

        $id = $form['album'];

        if( isset( $form["album"] ) ){
            if( $form["album"] == 0 && isset( $form['albumName'] ) ){
                $responsePostAlbum = (new FacebookRequest(
                    $this->fbSession, 'POST', '/me/albums', [
                        'name' => $_POST['form']['albumName']
                    ]
                ))->execute();

                $id = $responsePostAlbum->getGraphObject()->asArray()['id'];
            }
        }

        // Upload to a user's profile. The photo will be in the
        // first album in the profile. You can also upload to
        // a specific album by using /ALBUM_ID as the path
        $response = (new FacebookRequest(
            $this->fbSession, 'POST', '/' . $id . '/photos', $uploadOptions
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

    public function getAlbumPhotos( $id ) {
        $response = (new FacebookRequest(
            $this->fbSession, 'GET',  "/" . $id . "/photos"
        ))->execute();

        return $response->getGraphObject();
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

        return $response->getGraphObject();
    }

    /**
     * @param $photoId
     *
     * @return mixed
     */
    public function getLikeCount( $photoId ) {
        $fql  = "SELECT share_count, like_count, comment_count ";
        $fql .= " FROM link_stat WHERE url = '" . $this->request->getSchemeAndHttpHost() . "#" . $photoId . "'";

        $fqlURL = "https://api.facebook.com/method/fql.query?format=json&query=" . urlencode($fql);

        // Facebook Response is in JSON
        $response = file_get_contents($fqlURL);
        return json_decode($response);
    }

    /**
     * @param $photoId
     *
     * @return mixed
     * @throws \Facebook\FacebookRequestException
     */
    public function getPhoto( $photoId ) {
        $response = (new FacebookRequest(
            $this->fbSession, 'GET', '/' . $photoId
        ))->execute();

        return $response->getGraphObject();
    }
}