<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AfficheUtilisateurController extends AbstractController
{
    /**
     * @Route("/afficheutilisateur/{id}", name="affiche_utilisateur")
     */
    public function AfficheUtilisateur($id): Response
    {
        $user = new Participant();
        $tableRepo = $this->getDoctrine()->getManager()->getRepository(Participant::class);
        $user = $tableRepo->find($id);
        dd($user);
        return $this->render('affiche_utilisateur/afficheUtilisateur.html.twig', [
            'user' => $user,
        ]);
    }
}
