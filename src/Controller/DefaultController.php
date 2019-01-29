<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route ("/faq",name="faq")
     */
    public function faq()
    {
        return $this->render('default/faq.html.twig');
    }

    /**
     * @Route ("/cgu",name="cgu")
     */
    public function cgu()
    {
        return $this->render('default/cgu.html.twig');
    }

    /**
     * @Route ("/fonctionnement",name="how_to")
     */
    public function fonctionnement()
    {
        return $this->render('default/fonctionnement.html.twig');
    }
}
