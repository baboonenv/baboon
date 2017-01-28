<?php

namespace Baboon\PanelBundle\Entity;

/**
 * Class UploadFile
 * @package Baboon\PanelBundle\Entity
 */
class UploadFile
{
    protected $file;

    /**
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string|null $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}