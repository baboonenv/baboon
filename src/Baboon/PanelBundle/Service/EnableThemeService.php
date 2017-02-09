<?php

namespace Baboon\PanelBundle\Service;

use Baboon\PanelBundle\Params\AssetTypes;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EnableThemeService
 * @package Baboon\PanelBundle\Service
 */
class EnableThemeService
{
    /**
     * @var ToolsService
     */
    private $tools;

    /**
     * @var string
     */
    private $themeZipUri;

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
    private $cloneThemePath;

    /**
     * EnableThemeService constructor.
     *
     * @param ToolsService $toolsService
     */
    public function __construct(ToolsService $toolsService)
    {
        $this->tools = $toolsService;
    }

    /**
     * @param string $zipUri
     * @return bool
     */
    public function downloadAndEnableTheme(string $zipUri) : bool
    {
        $this->setupVars($zipUri);
        $zipFileContent = $this->tools->getContent($this->themeZipUri);
        $this->tools->createDir($this->themeDir);
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
        $this->tools->moveFilesToDir($this->themeDir, $this->tools->getSourceDir(), false);
        $this->tools->moveFilesToDir($this->themeDir, $this->tools->getRenderDir(), false);
        $this->generateDataFile();

        return true;
    }

    /**
     * @param string $zipUri
     */
    private function setupVars(string $zipUri)
    {
        $this->themeZipUri = $zipUri;
        $this->themeDir = $this->tools->getThemesDir().rand(0, 999).'/';
        $this->themeRealDir = $this->themeDir;
        $this->cloneThemePath = $this->themeDir.'theme_clone.zip';
    }

    private function setupSiteDirs()
    {
        $this->tools->deleteDir($this->tools->getSiteDir());
        $this->tools->createDir($this->tools->getSiteDir());
        $this->tools->createDir($this->tools->getSourceDir());
        $this->tools->createDir($this->tools->getRenderDir());
    }

    /**
     * @return bool
     */
    private function generateDataFile()
    {
        $baboonData = Yaml::parse(file_get_contents($this->tools->getSourceDir().'.baboon.yml'));
        $baboonData['assets'] = $this->normalizeConfigurationAssets($baboonData['assets']);

        file_put_contents($this->tools->getSiteDir().'data.json', json_encode($baboonData));

        return true;
    }

    private function normalizeConfigurationAssets($assets)
    {
        foreach ($assets as $assetKey => $asset){

            if($asset['type'] == AssetTypes::TREE){
                $assets[$assetKey]['assets'] = $this->normalizeConfigurationAssets($asset['assets']);

                continue;
            }
            $asset['value'] = $asset['default'];
            $asset['isDefaultValue'] = true;

            $assets[$assetKey] = $asset;
        }

        return $assets;
    }

    /**
     * @return bool
     */
    private function normalizeThemeDir()
    {
        if($this->tools->haveBaboonConfiguration($this->themeDir)){
            return true;
        }
        $scanThemeDir = scandir($this->themeDir);
        foreach ($scanThemeDir as $item){
            if(is_dir($this->themeDir.$item) && $item != '.' && $item != '..'){
                $this->themeDir .=  $item;
                if($this->tools->haveBaboonConfiguration($this->themeDir)){
                    $this->tools->moveFilesToDir($this->themeDir, $this->themeRealDir);
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
