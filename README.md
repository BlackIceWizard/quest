>docker compose exec -w/var/www/html/OwlOrm php composer phpunit

# RiverRing/OwlOrm play ground

RiverRing/OwlOrm: https://packagist.org/packages/river-ring/owl-orm

## Installation

#### Docker

Create `.env` and .env.local files:

```bash
$ make envfile
```


Create `docker-compose.override.yml` file:

```bash
$ make docker-compose.override.yml
```


Build, run and install:

```bash
$ make
```

Login into container:

```bash
$ make ssh
```

Init DB state:

```SQL
create table quests
(
    id         uuid         not null
        constraint quests_pk
            primary key,
    name       varchar      not null,
    created_at timestamp(0) not null
);

alter table quests
    owner to dev;

create unique index quests_id_uindex
    on quests (id);

create table quest_media
(
    id             uuid         not null
        constraint quest_media_pk
            primary key,
    name           varchar      not null,
    file_mime_type varchar      not null,
    file_size      integer      not null,
    file_location  varchar      not null,
    created_at     timestamp(0) not null,
    quest_id       uuid         not null
        constraint quest_media_quests_id_fk
            references quests
);

alter table quest_media
    owner to dev;

insert into quests (id, name, created_at)
values ('fbe2ea60-637d-4a4e-8a0a-3257a05ad146', 'first', NOW());

insert into quest_media (id, name, file_mime_type, file_size, file_location, created_at, quest_id)
values ('f920881d-6d7c-44aa-ba55-48fc97fd19d3', 'Cover', 'image/png', '100500', 'here/there', NOW(),
        'fbe2ea60-637d-4a4e-8a0a-3257a05ad146');
```

Run proof of concept code:
```bash
docker-compose exec php php bin/console proof-of-concept
```

Proof of concept code:

file: ```src/Bridge/Symfony/Console/ProofOfConceptCommand.php```

```PHP
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
```