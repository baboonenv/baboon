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

    public function collectConfigurationData()
    {
        $dataArray = json_decode(file_get_contents($this->getDataFile()), true);

        return $dataArray;
    }

    public function getDataFile()
    {
        return $this->kernel->getRootDir().'/../web/_site/data.json';
    }
}