<?php

namespace App\Controller\admin;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminRunController extends AbstractController
{

    #[Route('/admin/verified', name: 'verified-runs', methods: ['GET'])]
    public function displayVerifiedRuns(PostRepository $postRepository): Response
    {

        // Récupère uniquement les speedruns vérifiés
        $verifiedPosts = $postRepository->findBy(['verified' => true], ['publicationDate' => 'DESC']);

        return $this->render('admin/verified-runs.html.twig', ['posts' => $verifiedPosts]);
    }

    #[Route('/admin/unverified', name: 'unverified-runs', methods: ['GET'])]
    public function displayUnverifiedRuns(PostRepository $postRepository): Response
    {

        // Récupère uniquement les speedruns non vérifiés
        $unverifiedPosts = $postRepository->findUnverifiedOrNull();

        return $this->render('admin/unverified-runs.html.twig', ['posts' => $unverifiedPosts]);
    }

    #[Route('/admin/delete/speedrun/{id}', name: 'admin-delete-speedrun', methods: ['POST'])]
    public function deletePosts(
        int $id,
        Request $request,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $post = $postRepository->find($id);

        $entityManager->remove($post);
        $entityManager->flush();

        // 🔁 Revenir à la page précédente
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        // Fallback si le referer est absent
        return $this->redirectToRoute('unverified-runs');
    }

    #[Route('/admin/speedrun/edit/{id}', name: 'admin-edit-speedrun', methods: ['GET', 'POST'])]
    public function editSpeedrun(
        int $id,
        Request $request,
        PostRepository $postRepository,
        ActivityRepository $activityRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $post = $postRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException('Speedrun non trouvé.');
        }

        $categories = $categoryRepository->findAll();

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $activity = $activityRepository->find($data['activity'] ?? null);

            $post->updatePost($data, $activity);

            // ➕ Si le post est vérifié, on attribue l'utilisateur vérificateur
            if ($post->isVerified()) {
                $post->setVerifiedBy($this->getUser()); // 👈 ici on met l'admin connecté
                $post->setVerificationDate(new \DateTimeImmutable()); // tu peux aussi mettre cette ligne si tu veux une date
            } else {
                // Si jamais on revient à "non vérifié", on nettoie
                $post->setVerifiedBy(null);
                $post->setVerificationDate(null);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le speedrun a bien été mis à jour.');
            return $this->redirectToRoute('unverified-runs');
        }

        return $this->render('admin/edit-speedrun.html.twig', [
            'post' => $post,
            'categories' => $categories,
        ]);
    }
}
