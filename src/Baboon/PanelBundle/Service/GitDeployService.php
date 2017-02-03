<?php

namespace Baboon\PanelBundle\Service;

use Baboon\PanelBundle\Entity\GitConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Coyl\Git\Git;

/**
 * Class GitDeployService
 * @package Baboon\PanelBundle\Service
 */
class GitDeployService
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
     * @var GitConfiguration
     */
    private $GitConfiguration;

    /**
     * @var Git
     */
    private $git;

    /**
     * GitDeployService constructor.
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
        $this->git = new Git();
    }

    public function deployToGit()
    {
        $this->deployService->syncSiteTheme();
        $this->GitConfiguration = $this->em->getRepository(GitConfiguration::class)->findOneBy([]);
        $this->uploadFilesToGit($this->tools->getRenderDir(), $this->GitConfiguration->getRepo());
    }

    public function uploadFilesToGit($srcDir, $destinationRepo)
    {
        $repo = $this->git->init($srcDir);
        $repo->add('.');
        $repo->commit('Site updated '.date('d-m-Y H:m'));

        $repo->run('remote add origin '.$destinationRepo);
        $repo->push('origin', $this->GitConfiguration->getBranch(), true);
    }
}
