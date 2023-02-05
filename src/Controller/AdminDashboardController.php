<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\AdresseRepository;
use App\Repository\UserRepository;
use App\Service\CallMeteoApi;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN', message: "Vous n'avez pas le droit d'accéder à cette ressource")]
class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(UserRepository $ur, CallMeteoApi $meteoApi): Response
    {
        $users = $ur->findAll();
        $meteo = $meteoApi->getData(false, 49.258329, 4.031696);
        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'users' => $users,
            'meteo' => $meteo,
        ]);
    }

    #[Route('/admin/dashboard/adresses', name: 'app_admindashboard_showalladr')]
    public function showAllAdr(AdresseRepository $ar, CallMeteoApi $meteoApi): Response
    {
        $adrs = $ar->findBy([], ['id' => 'DESC']);
        $meteo = $meteoApi->getData(false, 49.258329, 4.031696);
        return $this->render('admin_dashboard/showAdr.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'adresses' => $adrs,
            'meteo' => $meteo,
        ]);
    }

    #[Route('/admin/dashboard/users/add', name: 'app_admindashboard_adduser')]
    public function addUser(ManagerRegistry $doctrine, Request $request, CallMeteoApi $meteoApi): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_dashboard', [
                'id' => $form->getData()->getId(),
            ]);
        }
        $meteo = $meteoApi->getData(false, 49.258329, 4.031696);
        return $this->renderForm('admin_dashboard/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'meteo' => $meteo,
            'title' => "Ajout d'un nouvel utilisateur"
        ]);
    }

    #[Route('/admin/dashboard/users/{id}/edit', name: 'app_admindashboard_edituser', requirements: ['id' => '\d+'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function editUser(User $user, ManagerRegistry $doctrine, Request $request, CallMeteoApi $meteoApi): Response {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_dashboard', [
                'id' => $user->getId(),
            ]);
        }
        $meteo = $meteoApi->getData(false, 49.258329, 4.031696);
        return $this->renderForm('admin_dashboard/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'meteo' => $meteo,
            'title' => "Mise à jour d'un utilisateur"
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/dashboard/users/{id}/delete', name: 'app_admindashboard_deleteuser', requirements: ['id' => '\d+'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function deleteUser(User $user, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_dashboard');

    }
}
