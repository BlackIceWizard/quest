<?php

use Ramsey\Uuid\Uuid;
use RiverRing\Quest\Bridge\Symfony\Kernel;
use RiverRing\Quest\Domain\Quest\QuestId;
use RiverRing\Quest\Infrastructure\Database\Repository\QuestRepository;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

/** @var QuestRepository $quests */
$quests = $kernel->getContainer()->get(QuestRepository::class);
$quest = $quests->findById(QuestId::fromString('fbe2ea60-637d-4a4e-8a0a-3257a05ad146'));
//dump($quest);

$quests->store($quest);
