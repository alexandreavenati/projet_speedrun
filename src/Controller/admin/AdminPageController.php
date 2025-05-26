<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPageController extends AbstractController {

    #[Route('/admin/home-admin', name:'home-admin', methods:['GET'])]
    public function displayAdminHome(): Response{

        return $this->render('admin/home-admin.html.twig');
    } 

    #[Route('/admin/error-404', name:'admin-404', methods: ['GET'])]
    public function display404(): Response {

        $html = $this->renderView('admin/admin-404.html.twig');
		
		return new Response($html, '404');
    }
}