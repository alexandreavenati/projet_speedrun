<?php

namespace App\Controller\guest;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{

    #[Route('/', name: 'home', methods: ['GET'])]
    public function displayGuestHome(
        ActivityRepository $activityRepository,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
        Request $request
    ): Response {
        $categories = $categoryRepository->findAll();
        $activities = $activityRepository->findBy(['category' => 1]);

        $categoryId = $request->query->get('category', 1); // Récupère l'ID de la catégorie depuis la requête
        $activityId = (int) $request->query->get('activity');

        // Si aucune activité n'est spécifiée dans la requête, on sélectionne la première activité de la catégorie actuelle
        if (!$activityId) {
            $firstActivity = $activityRepository->findOneBy(['category' => $categoryId]);
            $activityId = $firstActivity ? $firstActivity->getId() : 3; // Si pas d'activité trouvée, on met 3 par défaut
        }
        
        $activities = $activityRepository->findBy(['category' => $categoryId]);
        $posts = $postRepository->findByActivityOrderByDurationAsc($activityId);

        if ($categoryId) {
            $activities = $activityRepository->findBy(['category' => $categoryId]);
        } else {
            $activities = $activityRepository->findAll();
        }

        $filteredActivities = [];

        if ($activityId) {
            $filteredActivity = $activityRepository->find($activityId);
            if ($filteredActivity) {
                $filteredActivities = [$filteredActivity];
            }
        } else {
            $filteredActivities = $activities;
        }

        return $this->render('guest/home.html.twig', [
            'activities' => $activities,
            'filteredActivities' => $filteredActivities,
            'categories' => $categories,
            'selectedActivityId' => $activityId,
            'selectedCategoryId' => $categoryId,
            'posts' => $posts
        ]);
    }


    #[Route('/guest/error-404', name: 'guest-404', methods: ['GET'])]
    public function displayGuest404(): Response
    {
        $html = $this->renderView('guest/guest-404.html.twig');

        return new Response($html, '404');
    }
}
