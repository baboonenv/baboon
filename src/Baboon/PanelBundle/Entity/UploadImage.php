<?php

namespace Baboon\PanelBundle\Entity;

/**
 * Class UploadImage
 * @package Baboon\PanelBundle\Entity
 */
class UploadImage
{
    protected $image;

    /**
     * @return string|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
}