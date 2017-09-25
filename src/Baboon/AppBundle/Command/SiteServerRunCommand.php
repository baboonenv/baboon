<?php

namespace Baboon\AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ServerCommand;

/**
 * Class SiteServerRunCommand
 * @package Baboon\AppBundle\Command
 */
class SiteServerRunCommand extends ServerCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('address', InputArgument::OPTIONAL, 'Address:port', '127.0.0.1'),
                new InputOption('port', 'p', InputOption::VALUE_REQUIRED, 'Address port number', '8000'),
                new InputOption('docroot', 'd', InputOption::VALUE_REQUIRED, 'Document root', null),
                new InputOption('router', 'r', InputOption::VALUE_REQUIRED, 'Path to custom router script'),
            ))
            ->setName('baboon:site:server:run')
            ->setDescription('Runs PHP built-in web server')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $documentRoot = $input->getOption('docroot');

        if (null === $documentRoot) {
            $documentRoot = $this->getContainer()->getParameter('kernel.root_dir').'/../web';
        }

        if (!is_dir($documentRoot)) {
            $io->error(sprintf('The given document root directory "%s" does not exist', $documentRoot));

            return 1;
        }

        $env = $this->getContainer()->getParameter('kernel.environment');
        $address = $input->getArgument('address');

        if (false === strpos($address, ':')) {
            $address = $address.':'.$input->getOption('port');
        }

        if ($this->isOtherServerProcessRunning($address)) {
            $io->error(sprintf('A process is already listening on http://%s.', $address));

            return 1;
        }

        if ('prod' === $env) {
            $io->error('Running PHP built-in server in production environment is NOT recommended!');
        }

        $io->success(sprintf('Server running on http://%s', $address));
        $io->comment('Quit the server with CONTROL-C.');

        if (null === $builder = $this->createPhpProcessBuilder($io, $address)) {
            return 1;
        }

        $builder->setWorkingDirectory($documentRoot);
        $builder->setTimeout(null);
        $process = $builder->getProcess();

        if (OutputInterface::VERBOSITY_VERBOSE > $output->getVerbosity()) {
            $process->disableOutput();
        }

        $this
            ->getHelper('process')
            ->run($output, $process, null, null, OutputInterface::VERBOSITY_VERBOSE);

        if (!$process->isSuccessful()) {
            $errorMessages = array('Built-in server terminated unexpectedly.');

            if ($process->isOutputDisabled()) {
                $errorMessages[] = 'Run the command again with -v option for more details.';
            }

            $io->error($errorMessages);
        }

        return $process->getExitCode();
    }

    private function createPhpProcessBuilder(SymfonyStyle $io, $address)
    {
        $siteRenderDir = realpath($this->getContainer()->get('baboon.tools_service')->getRenderDir());

        $finder = new PhpExecutableFinder();

        if (false === $binary = $finder->find()) {
            $io->error('Unable to find PHP binary to run server.');

            return;
        }

        return new ProcessBuilder(array($binary, '-S', $address, '-t', $siteRenderDir));
    }
}
