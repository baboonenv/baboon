<?php

namespace Baboon\PanelBundle\Service;

use Baboon\PanelBundle\Params\AssetTypes;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ValidateConfigurationService
 * @package Baboon\PanelBundle\Service
 */
class ValidateConfigurationService
{
    /**
     * @var ToolsService
     */
    private $tools;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var string
     */
    private $configurationFile;

    /**
     * @var array
     */
    private $configurationData;

    /**
     * DeployThemeService constructor.
     *
     * @param ToolsService $toolsService
     */
    public function __construct(ToolsService $toolsService)
    {
        $this->tools = $toolsService;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function validate()
    {
        $this->setupVars();
        $isValidYml = $this->validateYml();
        if(!$isValidYml){
            goto returnErrors;
        }
        $this->validateInfo();
        $this->validateRenderFiles();
        $this->validateAssets();

        returnErrors:

        return $this->getErrors();
    }

    public function setConfigFile(string $configFile)
    {
        $this->configurationFile = $configFile;
    }

    private function setupVars()
    {
        if(empty($this->configurationFile)){
            $this->configurationFile = $this->tools->getSourceDir().'.baboon.yml';
        }
    }

    private function getErrors()
    {
        return $this->errors;
    }

    private function addError($message)
    {
        $this->errors[] = $message;
    }

    private function validateYml()
    {
        try{
            $this->configurationData = Yaml::parse(file_get_contents($this->configurationFile));
        }catch (ParseException $exception){
            $this->addError($exception->getMessage());

            return false;
        }

        return true;
    }

    private function validateInfo()
    {
        if(empty($this->accessor->getValue($this->configurationData, '[info]'))){
            $this->addError('Theme info param must be configured');
        }
        if(empty($this->accessor->getValue($this->configurationData, '[info][theme_name]'))){
            $this->addError('[info][thema_name] param must be configured');
        }
        $updateZipUrl = $this->accessor->getValue($this->configurationData, '[info][update_zip_uri]');
        $updateGitUrl = $this->accessor->getValue($this->configurationData, '[info][update_git_uri]');
        if(empty($updateZipUrl) && empty($updateGitUrl)){
            $this->addError('[info][update_zip_uri] or [info][update_zip_uri] param must be configured');
        }
    }

    private function validateRenderFiles()
    {
        if(empty($this->accessor->getValue($this->configurationData, '[render_files]'))){
            $this->addError('Less one [render_files] file must be specified');
        }
    }

    private function validateAssets()
    {
        $assets = $this->accessor->getValue($this->configurationData, '[assets]');
        $this->validateAssetsConfiguration($assets);
    }

    private function validateAssetsConfiguration($assets, $parent = '')
    {
        if(empty($assets)){
            $this->addError('[assets] param must be configured');
            return;
        }
        foreach ($assets as $asset){
            $this->validateAsset($asset);
        }
    }

    private function validateAsset($asset)
    {
        $this->varExists($asset, 'label');
        $type = $this->varExists($asset, 'type');
        if(!in_array($type, AssetTypes::getAssetTypes())){
            $this->addError(sprintf("[field] field must be on of \n %s \n you given {%s}", implode('|', AssetTypes::getAssetTypes()), $type));
        }
        if($type == AssetTypes::TREE){
            $this->varExists($asset, 'assets');
            $this->validateAssetsConfiguration($asset['assets']);
        }else{
            $this->varExists($asset, 'default');
        }

    }

    private function varExists($array, $var)
    {
        $getVar = $this->accessor->getValue($array, '['.$var.']');
        if(empty($getVar)){
            $this->addError(sprintf('[%s] field must be specified', $var));

            return false;
        }

        return $getVar;
    }
}

































