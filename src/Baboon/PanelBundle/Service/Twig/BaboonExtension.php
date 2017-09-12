<?php

namespace Baboon\PanelBundle\Service\Twig;

class BaboonExtension extends \Twig_Extension
{
    /**
     * BaboonExtension constructor.
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('transform_id', [$this, 'getTransformId']),
        ];
    }

    public function getFunctions()
    {
        return [];
    }

    public function getTransformId($path)
    {
        $id = str_replace(['[', ']'], '_', $path);

        return $id;
    }
}
