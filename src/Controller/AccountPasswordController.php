<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        return $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/modifier-mot-de-passe", name="app_password_modify")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = '';
        $user = $this->getUser(); //this is the hashed password from the db

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($user->getPassword()); //this is the same hashed password from the db as on line 26
            $oldPassword = $form->get('old_password')->getData();
            if($passwordHasher->isPasswordValid($user, $oldPassword)) {
                $newPassword = $form->get('new_password')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                //$this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = 'Votre mot de passe a bien ete mis a jour.';
            } else {
                $notification = 'Votre mot de passe actuel n\'est pas le bon.';
            }
        }

        return $this->render('account/modify-password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
