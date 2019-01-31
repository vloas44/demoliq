<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Message;
use App\Form\QuestionType;
use App\Form\MessageType;
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
     *     requirements={"id":"/d+"},
     *      methods ={'GET','POST'})
     */
    public function details(int $id, Request $request) {

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

        //Formulaire pour entrer des messages
        //Créer une instance de message à associer au formulaire
        $message= new Message();
        //Pour créer la relation entre les 2 entités
        $message->setQuestion($question);
        //Créer le formulaire
        $messageForm = $this->createForm(MessageType::class, $message);
        //Gérer la requête
        $messageForm->handleRequest($request);
        if($messageForm->isSubmitted()&& $messageForm->isValid()) {
            //Récupère l'entity manager:
            $em = $this->getDoctrine()->getManager();
            //On demande à Doctrine de sauvegarder notre instance :
            $em->persist($message);
            //pour exécuter :
            $em->flush();
            //Créer un message flash à afficher sur la prochaine page
            $this->addFlash('success', "Vous avez ajouter un message avec succès ! Merci pour votre participation");
            //redirige vers la page actuelle pour vider le formulaire
            return $this->redirectToRoute('question_detail',['id'=>$id]);
        }
        //récupère le messageRepository
        $messageRepository = $this->getDoctrine()->getRepository(Message::class);
        //récupère les 200 messages les + récents
        $messages = $messageRepository->findBy([
            'isPublished'=>true,
            'question'=>$question],//WHERE
            ['dateCreated'=>'DESC'],
            200);

        //$message->getQuestion()->getTitle();

        return $this->render('question/details.html.twig', [
            //passe les messages à twig
            'messageForm'=>$messageForm->createView(),
            //passe le message à twig
            'messages'=>$messages,
            //Passe la question à twig
            'question'=>$question]);
    }


    /**
     * @Route("/questions", name="question_list"), methods={"GET","POST"}
     */
    public function list()
    {
        //ce repository nous permet de faire des SELECT
        $questionRepository = $this->getDoctrine()->getRepository(Question::class);

        $questions=$questionRepository->findListQuestions();

        //SELECT * FROM question WHERE status = 'debating'
        //ORDER BY  supports DESC LIMIT 1000
        //$questions = $questionRepository->findBy(
            //['status' => 'debating'],   //where
            //['supports' => 'DESC'],     //order by
            //1000,                       //limit
            //0                           //offset
        //);

        //dd($questions);



        return $this->render('question/list.html.twig',[
            "questions" => $questions
        ]);
    }
}
