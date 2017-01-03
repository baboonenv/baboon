<?php

namespace Baboon\PanelBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Common methods for journal
 */
class EnableThemeService
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

    /**
     * @param string $zipUri
     * @return bool
     */
    public function downloadAndEnableTheme(string $zipUri) : bool
    {
        $this->setupVars($zipUri);
        $zipFileContent = $this->getContent($this->themeZipUri);
        $this->createDir($this->themeDir);
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
        $this->moveFilesToDir($this->themeDir, $this->siteDir.'_source/', false);
        $this->moveFilesToDir($this->themeDir, $this->siteDir.'_render/', false);

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
        $this->deleteDir($this->siteDir);
        $this->createDir($this->siteDir);
        $this->createDir($this->siteDir.'_source/');
        $this->createDir($this->siteDir.'_render/');
    }

    /**
     * @return bool
     */
    private function normalizeThemeDir()
    {
        if($this->haveBaboonConfiguration($this->themeDir)){
            return true;
        }
        $scanThemeDir = scandir($this->themeDir);
        foreach ($scanThemeDir as $item){
            if(is_dir($this->themeDir.$item) && $item != '.' && $item != '..'){
                $this->themeDir .=  $item;
                if($this->haveBaboonConfiguration($this->themeDir)){
                    $this->moveFilesToDir($this->themeDir, $this->themeRealDir);
                    $this->themeDir = $this->themeRealDir;

                    return true;
                }else{
                    throw new \LogicException('Theme must to specify .baboon.yml configuration file!');
                }
            }
        }

        return false;
    }

    /**
     * @param string $path1
     * @param string $path2
     * @param bool $removeFirstDir
     *
     * @return bool
     */
    private function moveFilesToDir(string $path1, string $path2, $removeFirstDir = true)
    {
        $path1 = realpath($path1);
        $path2 = realpath($path2);
        $path1ScanDir = $this->scanDirContentRecursive($path1);
        foreach ($path1ScanDir as $itemPath){
            if(is_file($itemPath)){
                $copyPath = str_replace($path1, $path2, $itemPath);
                $copyPathInfo = pathinfo($copyPath);
                $this->createDir($copyPathInfo['dirname']);
                copy($itemPath, $copyPath);
            }
        }
        if($removeFirstDir){
            $this->deleteDir($path1);
        }

        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function haveBaboonConfiguration(string $path) : bool
    {
        $scanThemeDir = scandir($path);
        foreach ($scanThemeDir as $item){
            if($item == '.baboon.yml'){
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $dir
     * @param array $results
     *
     * @return array
     */
    private function scanDirContentRecursive(string $dir, &$results = array())
    {
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                self::scanDirContentRecursive($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function deleteDir(string $path) : bool
    {
        if (!is_dir($path)) {
            return true;
        }
        if (substr($path, strlen($path) - 1, 1) != '/') {
            $path .= '/';
        }
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            if (is_dir($path.$file)) {
                self::deleteDir($path.$file);
            } else {
                unlink($path.$file);
            }
        }
        rmdir($path);

        return true;
    }

    private function createDir(string $path) : bool
    {
        if(is_dir($path)){
            return true;
        }
        mkdir($path, 0777, true);

        return true;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getContent(string $path)
    {
        return file_get_contents($path);
    }
}