<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        return $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="app_register")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = '';
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* $password = $form->get('password')->getData();
            $confirm_password = $form->get('confirm_password')->getData();

            if ($password === $confirm_password) {
                $hashed_password = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashed_password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = "L'inscription a ete effectue avec success !";
            } else {
                $notification = 'Le mot de passe et le de mot de passe confirme doivent etre identiques.';
            } */

            $db_user_array = $this->entityManager->getRepository(User::class)->findByEmail($form->get('email')->getData());

            if(!empty($db_user_array)) {
                $notification = "L'utilisateur existe deja.";
                } else {
                    $password = $form->get('password')->getData();
                    $hashed_password = $passwordHasher->hashPassword($user, $password);
                    $user->setPassword($hashed_password);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $notification = "L'inscription a ete effectue avec success !";
                }
            }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
