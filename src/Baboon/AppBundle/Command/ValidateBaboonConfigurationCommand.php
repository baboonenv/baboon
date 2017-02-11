<?php

namespace Baboon\AppBundle\Command;

use Baboon\PanelBundle\Service\ToolsService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ValidateBaboonConfigurationCommand
 * @package Bba\CoreBundle\Command
 */
class ValidateBaboonConfigurationCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ToolsService
     */
    private $tools;

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('baboon:configuration:validate')
            ->setDescription('Baboon configuration validation.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io           = new SymfonyStyle($input, $output);
        $this->container    = $this->getContainer();
        $this->em           = $this->container->get('doctrine')->getManager();
        $this->translator   = $this->container->get('translator');
        $this->tools        = $this->container->get('baboon.tools_service');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title($this->getDescription());
        $confData['baboon'] = $config1 = Yaml::parse(
            file_get_contents($this->tools->getSourceDir().'.baboon.yml')
        );
    }
}
