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
    public function postPhoto( $file ){
        $form = $_POST['form'];
        $uploadOptions = [ 'source' => new \CURLFile( $file ) ];

        if( !empty( $form['text'] ) ){
            $uploadOptions["message"] = $_POST['form']['text'];
        }

        $id = $form['album'];

        echo "\n<pre>"; var_dump("ok"); echo "</pre>";
        echo "\n<pre>"; var_dump($_POST['form']['albumName']); echo "</pre>";
        if( isset( $form["album"] ) ){
            if( $form["album"] == 0 && isset( $form['albumName'] ) ){
                $responsePostAlbum = (new FacebookRequest(
                    $this->fbSession, 'POST', '/me/albums', array(
                        'name' => $_POST['form']['albumName']
                    )
                ))->execute();

                $id = $responsePostAlbum->getGraphObject()->asArray()['id'];
            }
        }
echo "\n<pre>"; var_dump("2"); echo "</pre>";
        // Upload to a user's profile. The photo will be in the
        // first album in the profile. You can also upload to
        // a specific album by using /ALBUM_ID as the path
        $response = (new FacebookRequest(
            $this->fbSession, 'POST', '/' . $id . '/photos', $uploadOptions
        ))->execute();

        echo "\n<pre>"; var_dump(3); echo "</pre>";
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

        return $response->getGraphObject();
    }
}