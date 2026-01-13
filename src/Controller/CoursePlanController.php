<?php

namespace App\Controller;

use App\Entity\CoursePlan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Dompdf\Dompdf;
use Dompdf\Options;

#[IsGranted('ROLE_USER')]
class CoursePlanController extends AbstractController
{
    #[Route('/course-plan/{id}/pdf', name: 'app_course_plan_pdf')]
    public function exportPdf(CoursePlan $coursePlan): Response
    {
        // Vérifier que l'utilisateur est bien le propriétaire du syllabus lié au plan
        if ($coursePlan->getSyllabus()->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce plan de cours.");
        }

        // Configurer Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        // Générer le HTML
        $html = $this->renderView('course_plan/pdf.html.twig', [
            'coursePlan' => $coursePlan,
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // (Optionnel) Configurer la taille et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le PDF
        $dompdf->render();

        // Renvoyer le PDF au navigateur
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="plan_cours_' . $coursePlan->getId() . '.pdf"',
        ]);
    }

    #[Route('/course-plan/{id}', name: 'app_course_plan_show')]
    public function show(CoursePlan $coursePlan): Response
    {
        // Vérifier que l'utilisateur est bien le propriétaire du syllabus lié au plan
        if ($coursePlan->getSyllabus()->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce plan de cours.");
        }

        return $this->render('course_plan/show.html.twig', [
            'coursePlan' => $coursePlan,
        ]);
    }
}
