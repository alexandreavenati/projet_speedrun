<?php

namespace App\Controller\profile;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileUserController extends AbstractController
{

    #[Route('/profile/modifier-profil', name: 'update-profile', methods: ['GET', 'POST'])]
    public function displayUpdateProfile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ParameterBagInterface $params
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $newUsername = $request->request->get('update-username');
            $newBio = $request->request->get('update-bio');
            $newPassword = $request->request->get('_password');

            // Mise à jour du mot de passe si renseigné
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            // Upload d'image
            $imageFile = $request->files->get('update-image');
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($params->get('kernel.project_dir') . '/public/uploads', $newFilename);
                $user->setImage($newFilename);
            }

            // Mise à jour username et bio
            $user->updateProfile($newUsername, $user->getImage(), $newBio);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('home-profile');
        }

        return $this->render('profile/profile-update.html.twig', ['user' => $user]);
    }

    #[Route('/profile/supprimer', name: 'delete-profile', methods: ['GET'])]
    public function deleteProfile(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    ) {
        $user = $this->getUser();

        // Supprimer l'utilisateur
        $entityManager->remove($user);
        $entityManager->flush();

        // Déconnexion : suppression du token de sécurité
        $tokenStorage->setToken(null);

        // Invalidation de la session
        $session = $requestStack->getSession();
        $session->invalidate();

        // Rediriger vers la page d'accueil
        return $this->redirectToRoute('home');
    }
}
