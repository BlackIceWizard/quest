<?php
declare(strict_types=1);

namespace RiverRing\Quest\Bridge\Symfony\Console;

use Ramsey\Uuid\Uuid;
use RiverRing\Quest\Domain\File;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\QuestId;
use RiverRing\Quest\Infrastructure\Database\Repository\QuestRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProofOfConceptCommand extends Command
{
    private QuestRepository $quests;

    public function __construct(QuestRepository $quests)
    {
        parent::__construct();
        $this->quests = $quests;
    }

    protected function configure()
    {
        $this
            ->setName('proof-of-concept')
            ->setDescription('Loads Aggregate Root from the database, modifies it and saves it in the modified form.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $quest = $this->quests->findById(QuestId::fromString('fbe2ea60-637d-4a4e-8a0a-3257a05ad146'));

        $quest->rename($quest->name() . '_' . mb_strlen($quest->name()));

        foreach ($quest->media() as $media) {
            $media->rename($media->name() . '_' . mb_strlen($media->name()));
        }

        $quest->addMedia(new Media(Uuid::uuid4(), 'Some New', new File('image/png', 500100, 'some/where')));

        $this->quests->store($quest);

        return 0;
    }
}
