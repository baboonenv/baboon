<?php

namespace Baboon\AppBundle\Command;

use Baboon\PanelBundle\Entity\ThemeServer;
use Baboon\PanelBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpKernel\KernelInterface;

class SetupSandboxCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
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
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var Application
     */
    private $application;

    protected function configure()
    {
        $this
            ->setName('baboon:setup:sandbox')
            ->setDescription('Baboon Sandbox Setup!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io               = new SymfonyStyle($input, $output);
        $this->container        = $this->getContainer();
        $this->em               = $this->container->get('doctrine')->getManager();
        $this->kernel           = $this->container->get('kernel');
        $this->application      = new Application($this->kernel);
        $this->rootDir          = $this->kernel->getRootDir();
        $this->application->setAutoExit(false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title($this->getDescription());

        // sync database with system schema
        $command2 = 'doctrine:schema:update --force';
        $output->writeln('<info>Updating db schema!</info>');
        $this->application->run(new StringInput($command2));
        $this->io->newLine(1);

        // setup theme server
        $this->setupThemeServer();

        //setup sandbox user if not exits
        $this->setupSandboxUser();

        $this->setupConfigurationTheme();

        $output->writeln('<info>Have a good day! Install finished!</info>');
    }

    private function setupThemeServer()
    {
        $this->drawhr();

        $this->io->text('Searching for theme server configuration entry!');
        $themeServer = $this->em->getRepository(ThemeServer::class)->findOneBy([]);

        if($themeServer){
            $this->io->text('Theme server entry already exists!');

            return;
        }

        $serverUrl = 'http://server.baboonenv.com';
        $jsonConfiguration = file_get_contents($serverUrl.'/configuration');
        $configuration = json_decode($jsonConfiguration, true);
        $themeServer = new ThemeServer();
        $themeServer
            ->setName($configuration['name'])
            ->setUrl($serverUrl)
            ;
        $this->em->persist($themeServer);
        $this->em->flush();

        $this->io->text('Created theme server configuration!');
    }

    private function setupSandboxUser()
    {
        $this->drawhr();

        $this->io->text('Searching for sandbox user!');
        $adminUser = $this->em->getRepository(User::class)->findOneBy([
            'username' => 'sandbox',
        ]);

        if($adminUser){
            $this->io->text('Sandbox user entry exists skipped!');

            return;
        }

        $command = 'fos:user:create sandbox sandbox@baboonenv.com sandbox';
        $this->io->text('<info>Creating sandbox user!</info>');
        $this->application->run(new StringInput($command));
        $this->io->newLine(2);

        $this->io->text('Created sandbox user with <info>sandbox:sandbox</info> credentials!');
    }

    private function setupConfigurationTheme()
    {
        $this->drawhr();

        $enableThemeService = $this->getContainer()->get('baboon.panel.enable_theme_service');
        $zipUri = 'https://github.com/behram/BaboonConfigurationTheme/archive/master.zip';
        $enableThemeService->downloadAndEnableTheme($zipUri);

        $this->io->text('Configuration theme setup finished!');
    }

    private function drawhr()
    {
        $this->io->newLine();
        $this->io->text(str_repeat('-', 100));
        $this->io->newLine();
    }
}