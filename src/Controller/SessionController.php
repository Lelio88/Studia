<?php

namespace App\Controller;

use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/api/sessions/{id}/complete', name: 'api_session_complete', methods: ['PATCH'])]
    public function complete(
        Session $session,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['actualNotes'])) {
            $session->setActualNotes($data['actualNotes']);
        }

        $session->setDone(true);
        
        $em->flush();

        return $this->json([
            'message' => 'Session marquée comme terminée',
            'session_id' => $session->getId(),
            'done' => $session->isDone(),
            'actualNotes' => $session->getActualNotes()
        ]);
    }
}
