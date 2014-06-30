<?php

// vendor/ILP/BootstrapThemeBundle/Command/GenerateThemeCommand.php
namespace ILP\BootstrapThemeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
 
class GenerateThemeCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('generate:theme')
            ->setDescription('Generates the theme files for the currently active theme');
    }
 
    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // When the command is executed from CLI, 'web' is an acceptable input,
        // If running form a service, a full path needs to be provided
        // So we check if for "generate:theme" to detect if running from CLI
        $firstArg = $input->getFirstArgument();
        if ($firstArg === 'generate:theme') {
            $firstArg = 'web';
        }

        $text = 'Moving theme.css file to web dir and dumping output';
        $output->writeln($text);

        // Run the assets:install command to get our theme.css file in the web.dir
        $output->writeln('Running assets:install command\n');

        $assetsInstallCommand = $this->getApplication()->find('assets:install');
        $assetsInstallArgs = array(
            // Set the target to be the web directory
            'target' => $firstArg
        );

        $assetsReturnCode = $assetsInstallCommand->run(new ArrayInput($assetsInstallArgs), $output);
        if ($assetsReturnCode === 0) {
            $output->writeln('Assets installed!\n');
        } else {
            return $assetsReturnCode;
        }

        // Run the assetic:dump command so that assetic can serve out new theme.css file
        $asseticDumpCommand = $this->getApplication()->find('assetic:dump');
        $blankArgs = array(
            ''
        );
        $asseticDumpReturnCode = $asseticDumpCommand->run(new ArrayInput($blankArgs), $output);
        
        return $asseticDumpReturnCode;
    }
}