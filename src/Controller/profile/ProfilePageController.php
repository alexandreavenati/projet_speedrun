<?php

namespace App\Controller\profile;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilePageController extends AbstractController
{

    #[Route('/profile/home-profile', name: 'home-profile', methods: ['GET'])]
    public function displayProfileHome(): Response {

        $user = $this->getUser();

        return $this->render('profile/home-profile.html.twig', ['user'=>$user]);
    }

    #[Route('/profile/error-404', name: 'profile-404', methods: ['GET'])]
    public function display404(): Response
    {

        $html = $this->renderView('profile/profile-404.html.twig');

        return new Response($html, '404');
    }
}
