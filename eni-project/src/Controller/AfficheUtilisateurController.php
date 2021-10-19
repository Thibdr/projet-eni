<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AfficheUtilisateurController extends AbstractController
{
    /**
     * @Route("/afficheutilisateur/{id}", name="affiche_utilisateur" , methods={"GET"})
     */
    public function AfficheUtilisateur(Participant $participant): Response
    {
        return $this->render('affiche_utilisateur/afficheUtilisateur.html.twig', [
            'participant' => $participant,
        ]);
    }
}
