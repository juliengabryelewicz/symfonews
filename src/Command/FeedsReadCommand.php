<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\FeedRepository;
use App\Repository\NewsRepository;
use App\Entity\News;
use App\Service\NewsService;

#[AsCommand(
    name: 'feeds:read',
    description: 'Get All News from active feeds',
)]
class FeedsReadCommand extends Command
{

    use LockableTrait;

    private $feedRepository;
    private $newsRepository;
    private $newsService;

    public function __construct(FeedRepository $feedRepository, NewsRepository $newsRepository, NewsService $newsService)
    {
        $this->feedRepository = $feedRepository;
        $this->newsRepository = $newsRepository;
        $this->newsService = $newsService;

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

        if (!$this->lock()) {
            $output->writeln('Command is already running in another process.');
            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $output);

        $io->text('Importing News...');
        
        try{

            foreach($this->feedRepository->findBy(array("active" => true)) as $feed){

                $io->text('Importing News from '.$feed->getName().'...');

                $feed_content = simplexml_load_file($feed->getLink());

                $io->progressStart(count($feed_content->channel->item));
    
                foreach($feed_content->channel->item as $news_content){

                    // If news not available in database, we insert it
                    if(count($this->newsRepository->findBy(array("guid" => $news_content->guid))) == 0){

                        $news = $this->newsService->convertXmlIntoNews($news_content, $feed);
                        $this->newsRepository->save($news, true);

                    }

                    $io->progressAdvance();
                }

                $io->progressFinish();
    
            }

        }catch (\Exception $e){
            $io->error($e->getMessage());
            $this->release();
            return Command::FAILURE;

        }

        $io->success('All new articles from active feeds are saved');
        $this->release();
        return Command::SUCCESS;
    }
}
