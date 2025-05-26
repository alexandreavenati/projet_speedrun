<?php

namespace App\Controller\guest;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function displayLogin(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('guest/login.html.twig', ['error' => $error]);
    }

        #[Route('/register', name: 'registeration', methods: ['GET', 'POST'])]
public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    if ($request->isMethod('POST')) {
        $email = $request->request->get('_username');
        $username = $request->request->get('pseudonym');
        $plainPassword = $request->request->get('_password');

        $user = new User($email, $username);

        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Persister en BDD
        $entityManager->persist($user);
        $entityManager->flush();

        // Rediriger vers la page de login ou accueil
        return $this->redirectToRoute('login');
    }

    return $this->render('guest/register.html.twig');
}

    #[Route('/redirect-after-login', name: 'redirect_after_login', methods: ['GET'])]
    public function redirectAfterLogin(): Response
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home-admin');
        }

        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('home-profile');
        }

        // Redirection par défaut
        return $this->redirectToRoute('home');
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout() {}
}
