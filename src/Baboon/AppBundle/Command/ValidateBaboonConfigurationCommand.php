<?php

namespace Baboon\AppBundle\Command;

use Baboon\PanelBundle\Service\ValidateConfigurationService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ValidateBaboonConfigurationCommand
 * @package Bba\CoreBundle\Command
 */
class ValidateBaboonConfigurationCommand extends ContainerAwareCommand
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ValidateConfigurationService
     */
    private $validate;

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
        $this->validate     = $this->container->get('baboon.panel.validate_configuration');
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
        $errors = $this->validate->validate();
        if(count($errors)<1){
            $this->io->success('your.configuration.file.is.valid');

            return;
        }

        foreach ($errors as $error){
            $this->io->error($error);
        }

        return;
    }
}
