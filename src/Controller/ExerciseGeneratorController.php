<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\Session;
use App\Services\LlmClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExerciseGeneratorController extends AbstractController
{
    #[Route('/ai/generate-exercises/{id}', name: 'api_generate_exercises', methods: ['POST'])]
    public function generate(
        Session $session,
        LlmClient $llm,
        EntityManagerInterface $em
    ): JsonResponse {
        set_time_limit(180);

        // Context Construction
        $sessionData = [
            'title' => $session->getTitle(),
            'objectives' => $session->getObjectives(),
            'contents' => $session->getContents(),
            'activities' => $session->getActivities(),
            'course' => $session->getCoursePlan()->getTitle()
        ];
        
        $sessionContextJson = json_encode($sessionData, JSON_PRETTY_PRINT);
        
        // Load Prompt from file
        $systemPrompt = <<<EOF
Tu es un concepteur d'exercices pédagogiques.

Données :
- Contexte de la séance
- Compétence ciblée
- Niveau des étudiants
- Difficulté souhaitée

<SESSION_CONTEXT>
$sessionContextJson
</SESSION_CONTEXT>

Tâche :
Proposer une liste d'exercices adaptés (Génère exactement 3 exercices : 1 EASY, 1 MEDIUM, 1 HARD).

Réponds AU FORMAT JSON STRICT avec des clés en anglais pour la compatibilité technique :
{
  "exercises": [
    {
      "title": "Titre de l'exercice",
      "instruction": "Consigne claire...",
      "difficulty": "EASY|MEDIUM|HARD",
      "expectedDurationMinutes": 30,
      "correction": ["Point clé 1", "Point clé 2"]
    }
  ]
}
EOF;

        $userPrompt = "Génère des exercices pour la séance : " . $session->getTitle();

        try {
            $result = $llm->generateJson($systemPrompt, $userPrompt);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        $generatedCount = 0;
        if (isset($result['exercises']) && is_array($result['exercises'])) {
            foreach ($result['exercises'] as $exData) {
                $exercise = new Exercise();
                $exercise->setTitle($exData['title'] ?? 'Exercice');
                $exercise->setInstruction($exData['instruction'] ?? '');
                
                // Validate difficulty
                $diff = strtoupper($exData['difficulty'] ?? 'MEDIUM');
                if (!in_array($diff, ['EASY', 'MEDIUM', 'HARD'])) $diff = 'MEDIUM';
                $exercise->setDifficulty($diff);
                
                $exercise->setExpectedDurationMinutes($exData['expectedDurationMinutes'] ?? 15);
                $exercise->setCorrection($exData['correction'] ?? []);
                
                $exercise->setSession($session);
                
                $em->persist($exercise);
                $generatedCount++;
            }
            $em->flush();
        }

        return $this->json([
            'message' => "Génération réussie",
            'count' => $generatedCount
        ]);
    }
}
