<?php

namespace Baboon\PanelBundle\Service;

use Baboon\PanelBundle\Params\AssetTypes;

/**
 * Class DeployThemeService
 * @package Baboon\PanelBundle\Service
 */
class DeployThemeService
{
    /**
     * @var ThemeConfigurationService
     */
    private $configurationService;

    /**
     * @var ToolsService
     */
    private $tools;

    /**
     * @var \Mustache_Engine
     */
    private $mustache;

    /**
     * @var []
     */
    private $configurationData;

    /**
     * @var []
     */
    private $renderFiles = [];

    /**
     * @var []
     */
    private $renderData = [];

    /**
     * DeployThemeService constructor.
     *
     * @param ThemeConfigurationService $configurationService
     * @param ToolsService $toolsService
     * @param \Mustache_Engine $mustache
     */
    public function __construct(
        ThemeConfigurationService $configurationService,
        ToolsService $toolsService,
        \Mustache_Engine $mustache
    ) {
        $this->configurationService = $configurationService;
        $this->tools = $toolsService;
        $this->mustache = $mustache;
    }

    public function syncSiteTheme()
    {
        $this->setupRenderConfiguration();
        $this->renderFiles();
    }

    private function setupRenderConfiguration()
    {
        $this->configurationData = $this->configurationService->collectConfigurationData();

        foreach ($this->configurationData["render_files"] as $file){

            $this->renderFiles[] = $this->tools->getRenderDir().$file;
        }
        $this->tools->cleanDir($this->tools->getRenderDir());
        $this->tools->createDir($this->tools->getRenderDir());
        $this->tools->moveFilesToDir($this->tools->getSourceDir(), $this->tools->getRenderDir(), false);
        $this->normalizeRenderData();
    }

    private function normalizeRenderData()
    {
        $renderData = [];
        $renderData['container'] = $this->configurationData;
        $renderData = array_merge($renderData, $this->normalizeAssetsData($this->configurationData['assets']));

        $this->renderData = $renderData;
    }

    private function normalizeAssetsData($assets)
    {
        foreach ($assets as $assetKey => $asset){
            if(in_array($asset['type'], [AssetTypes::FILE, AssetTypes::IMAGE])){
                if(preg_match('/_uploads/', $asset['value'])){
                    $renderUploadPath = $this->moveUploadToRenderDir($asset['value']);
                    $asset['value'] = $renderUploadPath;
                }
            }
            if($asset['type'] == AssetTypes::TREE){
                $assets[$assetKey] = [];
                foreach ($asset['assets'] as $item){
                    $assets[$assetKey][] = $this->normalizeAssetsData($item);
                }
            }else{
                $assets[$assetKey] = $asset['value'];
            }
        }

        return $assets;
    }

    private function renderFiles()
    {
        foreach ($this->renderFiles as $file){
            $fileContent = $this->tools->getContent($file);
            $renderedContent = $this->mustache->render($fileContent, $this->renderData);
            file_put_contents($file, $renderedContent);
        }
    }

    private function moveUploadToRenderDir($path)
    {
        $fullPath = $this->tools->getWebDir().$path;
        $pathinfo = pathinfo($fullPath);
        $renderUploadsDir = $this->tools->getRenderDir().'_uploads/';
        $this->tools->createDir($renderUploadsDir);
        $copyPath = $renderUploadsDir.$pathinfo['basename'];
        copy($fullPath, $copyPath);

        return '_uploads/'.$pathinfo['basename'];
    }
}
