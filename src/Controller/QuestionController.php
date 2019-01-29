<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/questions/ajouter",
     *     name="question_add",
     *     methods={"GET","POST"})
     */

    //Pour le formulaire de création d'une question
    public function create(Request $request){
        $question = new Question();

        $questionForm = $this->createForm(QuestionType::class, $question);

        $questionForm->handleRequest($request);

        if($questionForm->isSubmitted()&& $questionForm->isValid()){
            //Retourne l'entity manager:
        $em = $this->getDoctrine()->getManager();

        //On demande à Doctrine de sauvegarder notre instance :
        $em->persist($question);

        //pour exécuter :
        $em->flush();

        //Créer un message flash à afficher sur la prochaine page
          $this->addFlash('success', "Vous avez ajouter une question avec succès ! Merci pour votre participation");

        //Redirige vers la page de détails
            return $this->redirectToRoute('question_detail', ['id'=>$question->getId()]);
        }

        return $this->render('question/create.html.twig',[
            "questionForm"=>$questionForm->createView()
        ]);
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
