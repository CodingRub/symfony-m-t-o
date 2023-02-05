<?php

namespace App\Controller;

use App\Service\CallMeteoApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, CallMeteoApi $meteoApi): Response
    {
        if ($this->getUser()) {
             return $this->redirectToRoute('app_adresse', [
                     'id' => $this->getUser()->getId(),
                 ]
             );
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $currentMeteo = $meteoApi->getData(true, 49.258329, 4.031696);
        return $this->render('security/login.html.twig',
            ['error' => $error,
            'currentMeteo' => $currentMeteo]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
