<?php

namespace App\Controller;

use App\Repository\SyllabusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TEACHER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(SyllabusRepository $syllabusRepository): Response
    {
        // Récupérer uniquement les syllabus de l'utilisateur connecté
        $user = $this->getUser();
        $syllabi = $syllabusRepository->findBy(['owner' => $user], ['createdAt' => 'DESC']);

        return $this->render('dashboard/index.html.twig', [
            'syllabi' => $syllabi,
        ]);
    }
}
