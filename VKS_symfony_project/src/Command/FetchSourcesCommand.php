<?php

namespace App\Command;

use App\Service\SourceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'FetchSources',
    description: 'Add a short description for your command',
)]
class FetchSourcesCommand extends Command
{
    private $sourceService;

    public function __construct(SourceService $sourceService)
    {
        parent::__construct();
        $this->sourceService = $sourceService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->sourceService->fetchAndStoreSources();
        $io->success('Sources fetched and stored.');

        return Command::SUCCESS;
    }
}
