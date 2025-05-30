<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NoCacheListener
{
    private TokenStorageInterface $tokenStorage;

    // Le constructeur reçoit le service TokenStorageInterface via l'injection de dépendances
    // Ce service permet d'accéder au token d'authentification actuel (l'utilisateur connecté)
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    // Cette méthode est appelée à chaque réponse envoyée par Symfony (kernel.response)
    public function onKernelResponse(ResponseEvent $event): void
    {
        // Récupère le token d'authentification actuel
        $token = $this->tokenStorage->getToken();

        // Vérifie si un utilisateur est connecté (token non nul et utilisateur pas anonyme)
        $user = $token && $token->getUser() !== 'anon.' ? $token->getUser() : null;

        // Si un utilisateur est connecté, on modifie les headers HTTP de la réponse
        if ($user) {
            // Récupère l'objet Response
            $response = $event->getResponse();

            // Désactive la mise en cache dans le navigateur et les proxies
            // 'no-cache' : demande au navigateur de toujours vérifier la validité du contenu auprès du serveur
            // 'no-store' : interdit la mise en cache totale (contenu sensible)
            // 'must-revalidate' : le cache doit être revalidé auprès du serveur avant d'être utilisé
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');

            // 'Pragma' est un header HTTP/1.0 pour désactiver le cache (compatibilité)
            $response->headers->set('Pragma', 'no-cache');

            // 'Expires' à 0 indique que la réponse est déjà expirée, donc non-cacheable
            $response->headers->set('Expires', '0');
        }
    }
}
