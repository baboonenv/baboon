<?php

namespace Baboon\PanelBundle\Service;

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
        $this->tools->deleteDir($this->tools->getRenderDir());
        $this->tools->createDir($this->tools->getRenderDir());
        $this->tools->moveFilesToDir($this->tools->getSourceDir(), $this->tools->getRenderDir(), false);
        $this->normalizeRenderData();
    }

    private function normalizeRenderData()
    {
        $renderData = [];
        $renderData['container'] = $this->configurationData;
        foreach ($this->configurationData['assets'] as $assetKey => $asset){
            $renderData[$assetKey] = $asset['value'];
        }

        $this->renderData = $renderData;
    }

    private function renderFiles()
    {
        foreach ($this->renderFiles as $file){
            $fileContent = $this->tools->getContent($file);
            $renderedContent = $this->mustache->render($fileContent, $this->renderData);
            file_put_contents($file, $renderedContent);
        }
    }
}
