<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 14/05/15
 * Time: 11:38
 */

namespace Doge\FacebookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @package Doge\FacebookBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="doge_image")
 */
class Image {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="link", type="string", length=255)
     */
    protected $link;

    /**
     * @var string
     * @ORM\Column(name="fb_user_id", type="string", length=255)
     */
    protected $fbUserId;

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getFbUserId()
    {
        return $this->fbUserId;
    }

    /**
     * @param string $fbUserId
     */
    public function setFbUserId($fbUserId)
    {
        $this->fbUserId = $fbUserId;
    }
}