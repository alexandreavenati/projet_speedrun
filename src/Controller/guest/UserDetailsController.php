<?php

namespace App\Controller\guest;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserDetailsController extends AbstractController
{

    #[Route('/detail/{id}', name: 'detail-user', methods: ['GET'])]
    public function detailUser(int $id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('guest/user-detail.html.twig',  ['user' => $user]);
    }
}
