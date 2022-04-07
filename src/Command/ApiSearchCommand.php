<?php

namespace AcMarche\Api\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class ApiSearchCommand extends Command
{
    protected static $defaultName = 'api:search';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('keyword', InputArgument::REQUIRED, 'keyword')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $keyword = $input->getArgument('keyword');

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }


}
