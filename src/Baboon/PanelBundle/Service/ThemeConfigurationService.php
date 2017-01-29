<?php

namespace Baboon\PanelBundle\Service;

/**
 * Class ThemeConfigurationService
 * @package Baboon\PanelBundle\Service
 */
class ThemeConfigurationService
{
    /**
     * @var ToolsService
     */
    private $tools;

    /**
     * ThemeConfigurationService constructor.
     *
     * @param ToolsService $toolsService
     */
    public function __construct(ToolsService $toolsService)
    {
        $this->tools = $toolsService;
    }

    public function collectConfigurationData()
    {
        $dataArray = json_decode(file_get_contents($this->getDataFile()), true);

        return $dataArray;
    }

    public function saveConfigurationData($confData)
    {
        file_put_contents($this->getDataFile(), json_encode($confData));

        return true;
    }

    public function getDataFile()
    {
        return $this->tools->getSiteDir().'data.json';
    }
}