<?php

namespace Baboon\PanelBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EnableThemeService
 * @package Baboon\PanelBundle\Service
 */
class EnableThemeService
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ToolsService
     */
    private $toolsService;

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
     * @param ToolsService $toolsService
     */
    public function __construct(KernelInterface $kernel, ToolsService $toolsService)
    {
        $this->kernel = $kernel;
        $this->toolsService = $toolsService;
    }

    /**
     * @param string $zipUri
     * @return bool
     */
    public function downloadAndEnableTheme(string $zipUri) : bool
    {
        $this->setupVars($zipUri);
        $zipFileContent = $this->toolsService->getContent($this->themeZipUri);
        $this->toolsService->createDir($this->themeDir);
        $this->setupSiteDirs();

        file_put_contents($this->cloneThemePath, $zipFileContent);
        $zip = new \ZipArchive();
        $res = $zip->open($this->cloneThemePath);
        if ($res === TRUE) {
            $zip->extractTo($this->themeDir);
            $zip->close();
            unlink($this->cloneThemePath);
        }
        $this->normalizeThemeDir();
        $this->toolsService->moveFilesToDir($this->themeDir, $this->siteDir.'_source/', false);
        $this->toolsService->moveFilesToDir($this->themeDir, $this->siteDir.'_render/', false);
        $this->generateDataFile();

        return true;
    }

    /**
     * @param string $zipUri
     */
    private function setupVars(string $zipUri)
    {
        $this->themeZipUri = $zipUri;
        $this->rootDir = $this->kernel->getRootDir();
        $this->themesDir = $this->rootDir.'/../web/_themes/';
        $this->themeDir = $this->themesDir.rand(0, 999).'/';
        $this->themeRealDir = $this->themeDir;
        $this->siteDir = $this->rootDir.'/../web/_site/';
        $this->cloneThemePath = $this->themeDir.'theme_clone.zip';
    }

    private function setupSiteDirs()
    {
        $this->toolsService->deleteDir($this->siteDir);
        $this->toolsService->createDir($this->siteDir);
        $this->toolsService->createDir($this->siteDir.'_source/');
        $this->toolsService->createDir($this->siteDir.'_render/');
    }

    /**
     * @return bool
     */
    private function generateDataFile()
    {
        $baboonData = Yaml::parse(file_get_contents($this->siteDir.'_source/.baboon.yml'));
        foreach ($baboonData['assets'] as $assetKey => $asset){
            $asset['value'] = $asset['default'];
            $asset['isDefaultValue'] = true;

            $baboonData['assets'][$assetKey] = $asset;
        }

        file_put_contents($this->siteDir.'data.json', json_encode($baboonData));

        return true;
    }

    /**
     * @return bool
     */
    private function normalizeThemeDir()
    {
        if($this->toolsService->haveBaboonConfiguration($this->themeDir)){
            return true;
        }
        $scanThemeDir = scandir($this->themeDir);
        foreach ($scanThemeDir as $item){
            if(is_dir($this->themeDir.$item) && $item != '.' && $item != '..'){
                $this->themeDir .=  $item;
                if($this->toolsService->haveBaboonConfiguration($this->themeDir)){
                    $this->toolsService->moveFilesToDir($this->themeDir, $this->themeRealDir);
                    $this->themeDir = $this->themeRealDir;

                    return true;
                }else{
                    throw new \LogicException('Theme must to specify .baboon.yml configuration file!');
                }
            }
        }

        return false;
    }
}