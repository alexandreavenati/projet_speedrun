<?php

namespace App\Controller\profile;

use App\Entity\Post;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostSubmissionController extends AbstractController
{

    #[Route('/profile/soumission', name: 'submit-post', methods: ['GET', 'POST'])]
    public function postSubmission(CategoryRepository $categoryRepository, Request $request, ActivityRepository $activityRepository, EntityManagerInterface $entityManager)
    {
        $categories = $categoryRepository->findAll(); // Chaque catégorie contient ses activités via la relation

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $videoUrl = $request->request->get('video_url');
            $videoDuration = $request->request->get('video_duration');
            $description = $request->request->get('description');
            $platform = $request->request->get('platform');
            $expansion = $request->request->get('expansion');
            $activityId = $request->request->get('activity');

            $user = $this->getUser();

            // Convertit "HH:MM:SS" en objet DateTime (sans la date)
            $duration = \DateTime::createFromFormat('H:i:s', $videoDuration);

            $activity = $activityRepository->find($activityId);

            $post = new Post($title, $videoUrl, $duration, $description, $platform, $expansion, $activity);
            $post->setUser($user);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('home'); // ou une autre route après soumission
        }

        return $this->render('profile/post-submission.html.twig', [
            'categories' => $categories,
        ]);
    }
}
