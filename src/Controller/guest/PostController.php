<?php

namespace App\Controller\guest;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    #[Route('/speedrun/{id}', name: 'show-speedrun', methods: ['GET'])]
    public function displaySpeedrunDetails(
        int $id,
        CommentRepository $commentRepository,
        PostRepository $postRepository
    ) {
        // Récupération du post
        $post = $postRepository->find($id);

        // Récupération des commentaires liés uniquement à CE post
        $comments = $commentRepository->findBy(['post' => $post]);

        return $this->render('guest/show-speedrun.html.twig', ['activity' => $post->getActivity(), 'comments' => $comments, 'post'=>$post]);
    }

    #[Route('/redirect/speedrun/{category}/{activity}', name: 'redirect-speedrun', methods: ['GET'])]
    public function redirectToSpeedrun(int $category, int $activity): Response
    {
        return $this->redirectToRoute('home', [
            'category' => $category,
            'activity' => $activity
        ]);
    }
}
