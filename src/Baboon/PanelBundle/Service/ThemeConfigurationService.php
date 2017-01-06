<?php

namespace Baboon\PanelBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ThemeConfigurationService
 * @package Baboon\PanelBundle\Service
 */
class ThemeConfigurationService
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * EnableThemeService constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }


}