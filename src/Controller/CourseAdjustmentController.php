<?php

namespace App\Controller;

use App\Entity\CoursePlan;
use App\Services\LlmClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CourseAdjustmentController extends AbstractController
{
    #[Route('/ai/update-course-plan/{id}', name: 'api_update_course_plan', methods: ['POST'])]
    public function update(
        CoursePlan $coursePlan,
        LlmClient $llm,
        EntityManagerInterface $em
    ): JsonResponse {
        set_time_limit(180);

        // 1. Préparer le contexte (sessions faites vs à faire)
        $doneSessions = [];
        $remainingSessions = [];

        foreach ($coursePlan->getSessions() as $session) {
            if ($session->isDone()) {
                $doneSessions[] = [
                    'index' => $session->getIndexNumber(),
                    'title' => $session->getTitle(),
                    'notes' => $session->getActualNotes()
                ];
            } else {
                $remainingSessions[] = [
                    'index' => $session->getIndexNumber(),
                    'title' => $session->getTitle(),
                    'objectives' => $session->getObjectives()
                ];
            }
        }

        if (empty($doneSessions)) {
            return $this->json(['message' => 'Aucune session terminée. Ajustement inutile.'], 400);
        }

        // 2. Définition du Prompt
        $systemPrompt = <<<EOF
Tu es un expert en ingénierie pédagogique. Ta mission est d'ajuster les SÉANCES RESTANTES d'un plan de cours en fonction de ce qui a été réellement fait lors des séances précédentes.
Tiens compte des retards, des concepts non compris ou des avancées plus rapides mentionnées dans les notes.

Le JSON de réponse doit STRICTEMENT suivre cette structure :
{
  "remainingSessions": [
    {
      "indexNumber": 2,
      "title": "Nouveau titre si besoin",
      "objectives": ["Objectif ajusté 1", "Objectif ajusté 2"],
      "contents": ["Contenu mis à jour"],
      "activities": ["Activités adaptées"],
      "resources": ["Ressources"],
      "plannedDurationMinutes": 240
    }
  ]
}
EOF;

        $userPrompt = "Plan de cours: " . $coursePlan->getTitle() . "\n";
        $userPrompt .= "Sessions TERMINÉES (Historique): " . json_encode($doneSessions) . "\n";
        $userPrompt .= "Sessions RESTANTES à ajuster: " . json_encode($remainingSessions);

        try {
            $result = $llm->generateJson($systemPrompt, $userPrompt);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        // 3. Mise à jour des sessions en base
        if (isset($result['remainingSessions']) && is_array($result['remainingSessions'])) {
            $sessionsInDb = $coursePlan->getSessions();
            
            foreach ($result['remainingSessions'] as $updatedData) {
                // Trouver la session correspondante en DB via l'indexNumber
                foreach ($sessionsInDb as $session) {
                    if (!$session->isDone() && $session->getIndexNumber() === ($updatedData['indexNumber'] ?? -1)) {
                        $session->setTitle($updatedData['title'] ?? $session->getTitle());
                        $session->setObjectives($updatedData['objectives'] ?? $session->getObjectives());
                        $session->setContents($updatedData['contents'] ?? $session->getContents());
                        $session->setActivities($updatedData['activities'] ?? $session->getActivities());
                        $session->setResources($updatedData['resources'] ?? $session->getResources());
                        $session->setPlannedDurationMinutes($updatedData['plannedDurationMinutes'] ?? $session->getPlannedDurationMinutes());
                    }
                }
            }
            $em->flush();
        }

        return $this->json([
            'message' => 'Plan de cours réajusté avec succès',
            'course_plan_id' => $coursePlan->getId(),
            'adjusted_sessions_count' => count($result['remainingSessions'] ?? [])
        ]);
    }
}
