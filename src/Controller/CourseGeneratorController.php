<?php

namespace App\Controller;

use App\Entity\CoursePlan;
use App\Entity\Session;
use App\Entity\Syllabus;
use App\Services\LlmClient; // Attention au namespace 'Services' au pluriel selon ton fichier
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; // Add import

#[IsGranted('IS_AUTHENTICATED_FULLY')] // Secure the controller
class CourseGeneratorController extends AbstractController
{
    #[Route('/ai/generate-course-plan/{id}', name: 'api_generate_course_plan', methods: ['POST'])]
    public function generate(
        Syllabus $syllabus, // Symfony récupère automatiquement le Syllabus via l'ID dans l'URL
        LlmClient $llm, 
        EntityManagerInterface $em
    ): JsonResponse {
        // Augmenter le temps d'exécution maximum pour cette opération qui peut être longue
        set_time_limit(180); // 3 minutes
        
        // 1. Définition du Prompt Système strict pour correspondre à tes Entités
        $systemPrompt = <<<EOF
Tu es un ingénieur pédagogique. Analyse le syllabus fourni et génère un plan de cours structuré au format JSON.
Le JSON doit STRICTEMENT respecter cette structure pour correspondre à la base de données :

{
  "title": "Plan de cours Mathématiques L1",
  "generalPlan": "Description détaillée du plan de cours : objectifs généraux, structure du cours, méthodologie pédagogique, progression des apprentissages sur toute la durée du cours. Minimum 200 mots.",
  "evaluationCriteria": ["Examen final (40%)", "Contrôle continu (30%)", "Participation (15%)", "Projet (15%)"],
  "nbSessionsPlanned": 12,
  "expectedTotalHours": 48,
  "sessions": [
    {
      "indexNumber": 1,
      "title": "Introduction aux concepts",
      "objectives": ["Comprendre les bases", "Identifier les acteurs"],
      "contents": ["Historique", "Définitions clés"],
      "activities": ["Cours magistral", "Quiz"],
      "resources": ["Diapositives", "Article PDF"],
      "plannedDurationMinutes": 240
    }
  ]
}

Génère un plan de cours COMPLET avec toutes les sessions prévues. Le generalPlan doit être détaillé et substantiel.

EOF;

        try {
            // On utilise le rawText de ton entité Syllabus
            $promptData = "Titre: " . $syllabus->getTitle() . "\n Contenu: " . $syllabus->getRawText();
            $result = $llm->generateJson($systemPrompt, $promptData);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        $coursePlan = new CoursePlan();
        $coursePlan->setTitle($result['title'] ?? 'Plan de cours ' . $syllabus->getTitle());
        $coursePlan->setGeneralPlan($result['generalPlan'] ?? 'Plan généré automatiquement');
        
        $coursePlan->setEvaluationCriteria($result['evaluationCriteria'] ?? []); 
        $coursePlan->setExpectedTotalHours($result['expectedTotalHours'] ?? 0);
        $coursePlan->setNbSessionsPlanned($result['nbSessionsPlanned'] ?? count($result['sessions'] ?? []));
        $coursePlan->setCreatedAt(new \DateTimeImmutable());
        
        $syllabus->setCoursePlan($coursePlan);

        if (isset($result['sessions']) && is_array($result['sessions'])) {
            foreach ($result['sessions'] as $sData) {
                $session = new Session();
                $session->setIndexNumber($sData['indexNumber'] ?? 1);
                $session->setTitle($sData['title'] ?? 'Session sans titre');
                $session->setPlannedDurationMinutes($sData['plannedDurationMinutes'] ?? 60);
                
                // Champs array
                $session->setObjectives($sData['objectives'] ?? []);
                $session->setContents($sData['contents'] ?? []);
                $session->setActivities($sData['activities'] ?? []);
                $session->setResources($sData['resources'] ?? []);
                
                // Valeurs par défaut obligatoires
                $session->setDone(false);

                // Relation
                $coursePlan->addSession($session);
            }
        }

        $em->persist($coursePlan);
        $em->flush(); // Save plan and sessions first

        // 2. Génération automatique des exercices pour chaque session
        set_time_limit(300); // 5 minutes

        foreach ($coursePlan->getSessions() as $session) {
            $this->generateExercisesForSession($session, $llm, $em);
        }
        
        $em->flush(); // Save all exercises

        return $this->json([
            'message' => 'Plan de cours et exercices générés avec succès',
            'course_plan_id' => $coursePlan->getId(),
            'nb_sessions' => $coursePlan->getSessions()->count()
        ], 201);
    }

    private function generateExercisesForSession(Session $session, LlmClient $llm, EntityManagerInterface $em): void
    {
        // Context light pour économiser des tokens
        $sessionContext = json_encode([
            'title' => $session->getTitle(),
            'objectives' => $session->getObjectives(),
            'course_title' => $session->getCoursePlan()->getTitle()
        ]);

        $systemPrompt = <<<EOF
Tu es un expert pédagogique. Pour la session décrite ci-dessous, génère 3 exercices (EASY, MEDIUM, HARD) au format JSON STRICT.
Context: $sessionContext

JSON attendu:
{
  "exercises": [
    {
      "title": "Titre court",
      "instruction": "Consigne...",
      "difficulty": "EASY|MEDIUM|HARD",
      "expectedDurationMinutes": 20,
      "correction": ["Point clé"]
    }
  ]
}
EOF;
        
        try {
            $result = $llm->generateJson($systemPrompt, "Génère les exercices.");
            
            if (isset($result['exercises']) && is_array($result['exercises'])) {
                foreach ($result['exercises'] as $exData) {
                    $exercise = new \App\Entity\Exercise();
                    $exercise->setTitle($exData['title'] ?? 'Exercice');
                    $exercise->setInstruction($exData['instruction'] ?? 'Consigne à définir');
                    
                    $diff = strtoupper($exData['difficulty'] ?? 'MEDIUM');
                    if (!in_array($diff, ['EASY', 'MEDIUM', 'HARD'])) $diff = 'MEDIUM';
                    $exercise->setDifficulty($diff);
                    
                    $exercise->setExpectedDurationMinutes($exData['expectedDurationMinutes'] ?? 15);
                    $exercise->setCorrection($exData['correction'] ?? []);
                    $exercise->setSession($session);
                    
                    $em->persist($exercise);
                }
            }
        } catch (\Exception $e) {
            // Silence is golden here to not break the whole process
        }
    }
}
