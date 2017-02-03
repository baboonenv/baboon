<?php

namespace Baboon\PanelBundle\Service;

use Baboon\PanelBundle\Entity\FTPConfiguration;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class FTPDeployService
 * @package Baboon\PanelBundle\Service
 */
class FTPDeployService
{
    /**
     * @var ToolsService
     */
    private $tools;

    /**
     * @var DeployThemeService
     */
    private $deployService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FTPConfiguration
     */
    private $FTPConfiguration;

    /**
     * @var
     */
    private $FTPConnection;

    /**
     * FTPDeployService constructor.
     * @param ToolsService $toolsService
     * @param DeployThemeService $deployService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ToolsService $toolsService,
        DeployThemeService $deployService,
        EntityManagerInterface $em
    ) {
        $this->tools = $toolsService;
        $this->deployService = $deployService;
        $this->em = $em;
    }

    public function deployToFTP()
    {
        $this->deployService->syncSiteTheme();
        $this->FTPConfiguration = $this->em->getRepository(FTPConfiguration::class)->findOneBy([]);
        $this->FTPConnect();
        $this->uploadFilesToFTP($this->tools->getRenderDir(), $this->FTPConfiguration->getPath());
    }

    public function uploadFilesToFTP($srcDir, $destinationDir)
    {
        $d = dir($srcDir);
        while($file = $d->read()) {
            if ($file != "." && $file != "..") {
                if (is_dir($srcDir."/".$file)) {
                    if (!@ftp_chdir($this->FTPConnection, $destinationDir."/".$file)) {
                        ftp_mkdir($this->FTPConnection, $destinationDir."/".$file);
                    }
                    $this->uploadFilesToFTP($srcDir."/".$file, $destinationDir."/".$file);
                } else {
                    ftp_put($this->FTPConnection, $destinationDir."/".$file, $srcDir."/".$file, FTP_BINARY);
                }
            }
        }
        $d->close();
    }

    private function FTPConnect()
    {
        $configuration = $this->FTPConfiguration;
        $this->FTPConnection = ftp_connect($configuration->getHostname());
        ftp_login($this->FTPConnection, $configuration->getUsername(), $configuration->getPassword());
        ftp_chdir($this->FTPConnection, $configuration->getPath());
    }
}
