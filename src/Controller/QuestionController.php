<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/questions", name="question_list")
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
