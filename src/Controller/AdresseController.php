<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Service\CallMeteoApi;
use DateTime;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{

    #[Route('/adresse/', name: 'app_adresse')]
    #[IsGranted('IS_AUTHENTICATED_FULLY', message: "Vous devez être connecté d'abord pour pouvoir faire ça")]
    public function index(CallMeteoApi $meteoApi): Response
    {
        $meteo = $meteoApi->getData(false, 49.258329, 4.031696);
        return $this->render('adresse/index.html.twig', [
            'meteo' => $meteo,
            'adresses' => $this->getUser()->getAdresses(),
        ]);
    }

    #[Route('/adresse/add', name: 'app_adresse_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY', message: "Vous devez être connecté d'abord pour pouvoir faire ça")]
    public function add(ManagerRegistry $doctrine, Request $request, CallMeteoApi $meteoApi): Response
    {
        $newContact = new Adresse();
        $form = $this->createForm(AdresseType::class, $newContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $newContact->setAuthor($form->get('author')->getData());
            } catch (\OutOfBoundsException $e) {
                $newContact->setAuthor($this->getUser());
            }
            $entityManager = $doctrine->getManager();
            $entityManager->persist($newContact);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }
        $meteo = $meteoApi->getData(false, 49.258329, 4.031696);
        return $this->renderForm('adresse/add.html.twig', [
            'form' => $form,
            'meteo' => $meteo,
            'adresses' => $this->getUser()->getAdresses(),
            ]);
    }

    #[Route('/adresse/{id}', name: 'app_adresse_show', requirements: ['id' => '\d+'])]
    #[ParamConverter('adresse', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('IS_AUTHENTICATED_FULLY', message: "Vous devez être connecté d'abord pour pouvoir faire ça")]
    public function show(Adresse $adresse, CallMeteoApi $meteoApi): Response
    {
        $authorAdr = $adresse->getAuthor();
        if ($authorAdr !== $this->getUser()) {
            return $this->redirectToRoute('app_adresse');
        }
        $meteo = $meteoApi->getData(false, $adresse->getLatitude(), $adresse->getLongitude());
        return $this->render('adresse/show.html.twig', [
            'adresse' => $adresse,
            'meteo' => $meteo,
            'adresses' => $this->getUser()->getAdresses(),
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY', message: "Vous devez être connecté d'abord pour pouvoir faire ça")]
    #[IsGranted('ROLE_ADMIN', message: "Vous n'avez pas la permission de mettre à jour cet élément")]
    #[Route('/adresse/{id}/edit', name: 'app_adresse_edit', requirements: ['id' => '\d+'])]
    #[ParamConverter('adresse', options: ['mapping' => ['id' => 'id']])]
    public function edit(Adresse $adresse, ManagerRegistry $doctrine, Request $request, CallMeteoApi $meteoApi): Response {
        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $editAdresse = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('app_adresse', [
                'id' => $editAdresse->getId(),
            ]);
        }
        $meteo = $meteoApi->getData(false, $adresse->getLatitude(), $adresse->getLongitude());
        return $this->renderForm('adresse/edit.html.twig', [
            'adresse' => $adresse,
            'form' => $form,
            'meteo' => $meteo,


        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY', message: "Vous devez être connecté d'abord pour pouvoir faire ça")]
    #[Route('/adresse/{id}/delete', name: 'app_adresse_delete', requirements: ['id' => '\d+'])]
    #[ParamConverter('adresse', options: ['mapping' => ['id' => 'id']])]
    public function delete(Adresse $adresse, ManagerRegistry $doctrine): Response
    {
        $authorAdr = $adresse->getAuthor();
        if ($authorAdr !== $this->getUser()) {
            return $this->redirectToRoute('app_adresse');
        }
        $entityManager = $doctrine->getManager();
        $entityManager->remove($adresse);
        $entityManager->flush();
        return $this->redirectToRoute('app_home');

    }

}
