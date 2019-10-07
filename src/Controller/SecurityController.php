<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/admin/test", name="adminator")
     */
    public function adminsys()
    {
        return new Response("<h1> ADMIN </h1>");
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(ObjectManager $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            //$user->setRoles(['ROLE_USER, 'ROLE_ADMIN']);

            // ajout dans la base :
            $manager->persist($user);
            $manager->flush();
            // redirection sur la vue de l'article
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'controller_name' => 'SecurityController',
            'form' =>  $form->createView(),
        ]);
    }
}
