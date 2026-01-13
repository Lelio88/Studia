<?php

namespace App\Controller;

use App\Entity\CoursePlan;
use App\Services\LlmClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/ai/chat/{id?}', name: 'api_course_chat', methods: ['POST'])]
    public function chat(
        ?CoursePlan $coursePlan,
        Request $request,
        LlmClient $llm
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        if (empty($userMessage)) {
            return $this->json(['error' => 'Message vide'], 400);
        }

        // 1. Build Context
        if ($coursePlan) {
            $context = "Tu es un assistant pédagogique expert. Tu aides un formateur sur son cours spécifiques.\n";
            $context .= "CONTEXTE DU COURS :\n";
            $context .= "Titre : " . $coursePlan->getTitle() . "\n";
            $context .= "Syllabus (extrait) : " . substr($coursePlan->getSyllabus()->getRawText(), 0, 1500) . "...\n"; 
            
            $context .= "SÉANCES :\n";
            foreach ($coursePlan->getSessions() as $session) {
                $status = $session->isDone() ? "(FAIT)" : "(À FAIRE)";
                $context .= "- Séance ".$session->getIndexNumber()." ".$status." : ".$session->getTitle()."\n";
            }
            $systemPrompt = $context . "\n\nRéponds de manière concise, pratique et pédagogique à la question du formateur sur ce cours.";
        } else {
            // Prompt Généraliste
            $systemPrompt = "Tu es un assistant pédagogique expert et polyvalent. Tu aides un enseignant dans sa préparation de cours, sa méthodologie, la gestion de classe ou ses questions sur la pédagogie en général. Tu ne connais pas de cours spécifique pour le moment. Sois inspirant, structuré, bienveillant et concis.";
        }

        try {
            $responseContent = $llm->generateText($systemPrompt, $userMessage);
            return $this->json(['response' => $responseContent]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}