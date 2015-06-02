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
     * @ORM\Column(type="string")
     */
    protected $postId;

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
    }
}