<?php

namespace Baboon\PanelBundle\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Baboon\PanelBundle\Params\AssetTypes;

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

    public function addTreeItem(string $assetPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $confData = $this->collectConfigurationData();
        $asset = $accessor->getValue($confData, $assetPath);
        $originalAssets = $asset['original_assets'];
        $randomString = $this->generateRandomString(5);
        $normalizedAssets = $this->normalizeConfigurationAssets($originalAssets, $assetPath.'[assets]['.$randomString.']');

        $accessor->setValue($confData, $assetPath.'[assets]['.$randomString.']', $normalizedAssets);

        $this->saveConfigurationData($confData);

        return $normalizedAssets;
    }

    private function normalizeConfigurationAssets($assets, $path = '[assets]')
    {
        foreach ($assets as $assetKey => $asset){
            $asset['path'] = $path.'['.$assetKey.']';
            if($asset['type'] == AssetTypes::TREE){

                $randString = $this->generateRandomString(5);
                $itemPath = $asset['path'].'[assets]['.$randString.']';
                $asset['multiple'] = true;
                $fieldAssets = $asset['assets'];
                $asset['original_assets'] = $fieldAssets;
                $asset['assets'] = null;
                $asset['assets'][$randString] = $this->normalizeConfigurationAssets($fieldAssets, $itemPath);
            }else{
                $asset['value'] = $asset['default'];
                $asset['isDefaultValue'] = true;
            }
            $assets[$assetKey] = $asset;
        }

        return $assets;
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}