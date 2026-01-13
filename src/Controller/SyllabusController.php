<?php

namespace App\Controller;

use App\Entity\Syllabus;
use App\Form\SyllabusType;
use Doctrine\ORM\EntityManagerInterface;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Services\LlmClient; // Add import

#[IsGranted('ROLE_USER')]
class SyllabusController extends AbstractController
{
    #[Route('/syllabus/new', name: 'app_syllabus_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, LlmClient $llm): Response
    {
        $syllabus = new Syllabus();
        $form = $this->createForm(SyllabusType::class, $syllabus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('pdfFile')->getData();

            if ($pdfFile) {
                try {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($pdfFile->getPathname());
                    $text = $pdf->getText();
                    $syllabus->setRawText($text);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'extraction du PDF : ' . $e->getMessage());
                    return $this->render('syllabus/new.html.twig', [
                        'syllabus' => $syllabus,
                        'form' => $form,
                    ]);
                }
            }

            if (!$syllabus->getRawText()) {
                $this->addFlash('error', 'Le contenu du syllabus ne peut pas être vide (veuillez uploader un PDF ou saisir du texte).');
                return $this->render('syllabus/new.html.twig', [
                    'syllabus' => $syllabus,
                    'form' => $form,
                ]);
            }

            // Lier le syllabus à l'utilisateur connecté
            $syllabus->setOwner($this->getUser());
            
            // 1. Sauvegarde initiale
            $entityManager->persist($syllabus);
            $entityManager->flush();

            // 2. Analyse Automatique IA (Intégrée)
            try {
                $systemPrompt = <<<EOF
Tu es un expert pédagogique. Analyse le contenu brut de ce syllabus et extrait les informations structurées suivantes au format JSON strict :
{
  "extractedCompetences": ["Compétence 1", "Compétence 2", "Compétence 3"],
  "suggestedLevel": "Débutant / Intermédiaire / Avancé",
  "summary": "Résumé concis du cours en 2 phrases."
}
EOF;
                $promptData = "Titre: " . $syllabus->getTitle() . "\nContenu: " . substr($syllabus->getRawText(), 0, 5000);
                
                // Augmentation du timeout pour l'analyse
                set_time_limit(60);
                
                $result = $llm->generateJson($systemPrompt, $promptData);
                
                $syllabus->setExtractedCompetences($result['extractedCompetences'] ?? []);
                // On pourrait aussi sauvegarder le résumé si l'entité avait un champ 'description' ou 'summary'
                
                $entityManager->flush(); // Mise à jour avec les compétences
                
                $this->addFlash('success', 'Syllabus créé et analysé avec succès !');
            } catch (\Exception $e) {
                // Si l'IA échoue, on ne bloque pas la création, mais on avertit
                $this->addFlash('warning', 'Syllabus créé, mais l\'analyse IA a échoué : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('syllabus/new.html.twig', [
            'syllabus' => $syllabus,
            'form' => $form,
        ]);
    }
}