<?php

namespace App\Controller;

use App\Service\CallMeteoApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CallMeteoApi $meteoApi): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_adresse');
        }
        $currentMeteo = $meteoApi->getData(true, 49.258329, 4.031696);
        return $this->render('home/index.html.twig', [
            'currentMeteo' => $currentMeteo,
        ]);
    }
}
