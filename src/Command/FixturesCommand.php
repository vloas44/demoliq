<?php

namespace App\Command;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixturesCommand extends Command
{
    protected static $defaultName = 'app:fixtures';

    protected $em=null;

    public function __construct(EntityManagerInterface $em, ?string $name = null)
    {
        $this->em = $em;
        parent::__construct($name);
    }

    //Se lance au chargement de Symfony
    protected function configure()
    {
        $this->setDescription('Load dummy data in our database');
    }
    //lorsqu'on tape la commande
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->text("Coucou");
        $io->success("Now loading fixtures");

        $faker= \Faker\Factory::create('fr_FR');

        $conn = $this->em->getConnection();
        //Désactive la vérification des clefs étrangères
        $conn->query('SET FOREIGN_KEY_CHECKS=0');
        $conn->query('TRUNCATE message');
        $conn->query('TRUNCATE question');
        //Réactive
        $conn->query('SET FOREIGN_KEY_CHECKS=1');

        for ($i=0;$i<100;$i++){
            $question = new Question();

            $question->setTitle($faker->sentence(10));
            $question->setDescription($faker->realText(5000));
            $question->setStatus($faker->randomElement(['debating','voting','closed']));
            $question->setCreationDate($faker->dateTimeBetween('-1 year','now'));
            $question->setSupports($faker->optional(0.5,0)->numberBetween(0,47000000));

            $this->em->persist($question);
        }
        $this->em->flush();
        $io->success("Done!");
    }
}
