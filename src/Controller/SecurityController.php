<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register", methods={"GET","POST"})
     */
    public function register(UserPasswordEncoderInterface $encoder, Request $request){
        $this->getUser();

        $user= new User();

        $registerForm = $this->createForm(RegisterType::class, $user);

        $registerForm->handleRequest($request);

        if($registerForm->isSubmitted()&& $registerForm->isValid()){

            $password= $user->getPassword();
            $hash=$encoder->encodePassword($user, $password);
            $user->setPassword($hash);

            //Retourne l'entity manager:
            $em = $this->getDoctrine()->getManager();

            //On demande à Doctrine de sauvegarder notre instance :
            $em->persist($user);

            //pour exécuter :
            $em->flush();

            //Créer un message flash à afficher sur la prochaine page
            $this->addFlash('success', "Vous êtes enregistré avec succès ! ");

            //Redirige vers la page de détails
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig',[
            "registerForm"=>$registerForm->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/profil", name="mon_compte")
     */
    public function profil()
    {
        return $this->render('security/profil.html.twig');
    }

    /**
     * @Route("/deconnexion", name="app_logout")
     */
    public function logout (){}
}
