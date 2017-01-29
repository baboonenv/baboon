<?php

namespace Baboon\PanelBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DeployThemeService
 * @package Baboon\PanelBundle\Service
 */
class DeployThemeService
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $themeZipUri;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $themesDir;

    /**
     * @var string
     */
    private $themeRealDir;

    /**
     * @var string
     */
    private $themeDir;

    /**
     * @var string
     */
    private $siteDir;

    /**
     * @var string
     */
    private $cloneThemePath;

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
