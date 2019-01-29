<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/questions/ajouter",
     *     name="question_add",
     *     methods={"GET","POST"})
     */

    //Pour le formulaire de création d'une question
    public function create(){
        $question = new Question();

        $question->setTitle('blabla');
        $question->setDescription('pif paf pouf');
        $question->setStatus('debated');
        $question->setSupports(123);

        //Pour DateTime de php et non celui du dossier, mettre un backslash
        $question->setCreationDate(new \DateTime());

        //Retourne l'entity manager:
        $em = $this->getDoctrine()->getManager();

        //On demande à Doctrine de sauvegarder notre instance :
        $em->persist($question);

        //pour exécuter :
        $em->flush();

        return $this->render('question/create.html.twig',[

        ]);
        //return new Response ('OK')
        //Pour supprimer qqch de la BDD : $em->remove($question);
    }


    /**
     * @Route("/questions/{id}",
     *     name="question_detail"),
     *     requirements={"id":"/d+"}
     */
    public function details(int $id) {

        $questionRepository = $this->getDoctrine()->getRepository(Question::class);
        //SELECT * FROM question WHERE status = 'debating'
        //ORDER BY  supports DESC LIMIT 1000


        $question = $questionRepository->find($id);
        // ou $question = $questionRepository->findOneBy(["id"=> $id]);
        // ou sinon : public function details (Question question){}
        // ou $question = $questionRepository->findOneById($id);

        if(!$question){
            throw $this->createNotFoundException("Cette question n'existe pas !");
        }

        return $this->render('question/details.html.twig', ['question'=>$question]);
    }


    /**
     * @Route("/questions", name="question_list"), methods={"GET","POST"}
     */
    public function list()
    {
        //ce repository nous permet de faire des SELECT
        $questionRepository = $this->getDoctrine()->getRepository(Question::class);
        //SELECT * FROM question WHERE status = 'debating'
        //ORDER BY  supports DESC LIMIT 1000
        $questions = $questionRepository->findBy(
            ['status' => 'debating'],   //where
            ['supports' => 'DESC'],     //order by
            1000,                       //limit
            0                           //offset
        );

        //dd($questions);



        return $this->render('question/list.html.twig',[
            "questions" => $questions
        ]);
    }
}
