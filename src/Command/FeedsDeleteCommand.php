<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Repository\NewsRepository;
use App\Entity\News;

#[AsCommand(
    name: 'feeds:delete',
    description: 'Delete news superior to number of days',
)]
class FeedsDeleteCommand extends Command
{

    private $newsRepository;
    private $params;

    public function __construct(NewsRepository $newsRepository, ParameterBagInterface $params)
    {
        $this->newsRepository = $newsRepository;
        $this->params = $params;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to read all your active feeds')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Deleting News...');

        $nb_days = $this->params->get("nb_days");

        try{

            $this->newsRepository->removeOldNews($nb_days);

        }catch (\Exception $e){
            $io->error($e->getMessage());
            return Command::FAILURE;

        }

        $io->success('All news superior to '.$nb_days.' days have been deleted');

        return Command::SUCCESS;
    }
}
