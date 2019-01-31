<?php

namespace App\Command;

use App\Entity\Question;
use App\Entity\Subject;
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

        $answer=$io->ask("sure truncating ?");
        if($answer==="no"){
            $io->text("abort");
            die();
        }

        $conn = $this->em->getConnection();
        //Désactive la vérification des clefs étrangères
        $conn->query('SET FOREIGN_KEY_CHECKS=0');
        $conn->query('TRUNCATE subject');
        $conn->query('TRUNCATE message');
        $conn->query('TRUNCATE question');
        $conn->query('TRUNCATE question_subject');
        //Réactive
        $conn->query('SET FOREIGN_KEY_CHECKS=1');

        $io->text("tables are now empty.I hope you are not in prod !");

        $subjects = [
            "Affaires étrangères", "Affaires européennes", "Agriculture, alimentation, pêche", "Ruralité","Aménagement du territoire","Économie et finance","Culture","Communication","Défense","Écologie et développement durable","Transports","Logement","Éducation","Intérieur","Outre-mer et collectivités territoriales","Immigration","Justice et Libertés","Travail","Santé","Démocratie"];

        //Garder en mémoire nos objets Subject
        $allsubjects =[];
        foreach ($subjects as $label){
            $subject = new Subject();
            $subject->setLabel($label);
            $this->em->persist($subject);
            //On ajoute ce sujet à notre tableau
            $allsubjects[]= $subject;
        }
        $this->em->flush();
        //Barre de progression avec 200 opérations
        $io->progressStart(200);

        for ($i=0;$i<200;$i++){
            $io->progressAdvance(1);
            $question = new Question();

            $question->setTitle($faker->sentence(10));
            $question->setDescription($faker->realText(5000));

            //Ajoute entre 1 et 3 sujets à cette question
            $num=mt_rand(1,3);
            for ($j=0;$j<$num;$j++){
                $s=$faker->randomElement(($allsubjects));
                $question->addSubject($s);
            }


            $question->setStatus($faker->randomElement(['debating','voting','closed']));
            $question->setCreationDate($faker->dateTimeBetween('-1 year','now'));
            $question->setSupports($faker->optional(0.5,0)->numberBetween(0,47000000));

            //Ajouter des messages sur les questions (coller le code de Guillaume ici)******


            $this->em->persist($question);
        }
        $io->progressFinish();

        $this->em->flush();

        $io->success("Done!");
    }
}
