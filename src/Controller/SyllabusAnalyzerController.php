<?php

namespace App\Controller;

use App\Entity\Syllabus;
use App\Services\LlmClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SyllabusAnalyzerController extends AbstractController
{
    #[Route('/ai/analyze-syllabus/{id}', name: 'api_analyze_syllabus', methods: ['POST'])]
    public function analyze(
        Syllabus $syllabus,
        LlmClient $llm,
        EntityManagerInterface $em
    ): JsonResponse {
        set_time_limit(60);

        $systemPrompt = <<<EOF
Tu es un expert pédagogique. Analyse le contenu brut de ce syllabus et extrait les informations structurées suivantes au format JSON strict :

{
  "extractedCompetences": ["Compétence 1", "Compétence 2", "Compétence 3"],
  "suggestedLevel": "Débutant / Intermédiaire / Avancé",
  "summary": "Résumé concis du cours en 2 phrases."
}
EOF;

        try {
            $promptData = "Titre: " . $syllabus->getTitle() . "\nContenu: " . substr($syllabus->getRawText(), 0, 5000);
            $result = $llm->generateJson($systemPrompt, $promptData);
            
            // Mise à jour de l'entité
            $syllabus->setExtractedCompetences($result['extractedCompetences'] ?? []);
            // On pourrait ajouter d'autres champs à l'entité Syllabus si on voulait stocker le niveau, 
            // mais pour l'instant on stocke les compétences.
            
            $em->flush();

            return $this->json([
                'message' => 'Analyse terminée',
                'competences' => $syllabus->getExtractedCompetences(),
                'summary' => $result['summary'] ?? ''
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
