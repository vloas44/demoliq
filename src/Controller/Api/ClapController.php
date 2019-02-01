<?php

namespace App\Controller\Api;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ClapController extends AbstractController
{
    /**
     * @Route("/api/message/{id}/clap", name="api_clap_post", methods={"POST"})
     */
    public function addClap($id)
    {
        $em=$this->getDoctrine()->getManager();
        $repository = $em->getRepository(Message::class);

        $message = $repository->find($id);
        $message->setClaps($message->getClaps()+1);
        $em->flush();

        return new JsonResponse([
            "status" => "ok",
            "message" => "",
            "data" => [
                "claps" => $message->getClaps()
            ]
        ]);
    }

    /**
     * @Route("/api/message/{id}/clap", name="api_clap_post", methods={"GET"})
     */
    public function getClap(Message $message)
    {
             return new JsonResponse([
            "status" => "ok",
            "message" => "",
            "data" => [
                "claps" => $message->getClaps()
            ]
        ]);
    }

}