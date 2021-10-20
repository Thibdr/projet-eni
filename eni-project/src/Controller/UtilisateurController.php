<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModificationUtilisateurType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/modificationUtilisateur", name="utilisateur")
     */
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        if($this->getUser() != null) {
        $user = new Participant();
        $user = $this->getUser();
        $form = $this->createForm(ModificationUtilisateurType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($form->get('pseudo')->getData() != null && $form->get('pseudo')->getData() != $user->getPseudo() ){
                $user->setPseudo($form->get('pseudo')->getData());
            }
            if($form->get('prenom')->getData() != null){
                $user->setPrenom($form->get('prenom')->getData());
            }
            if($form->get('nom')->getData() != null){
                $user->setNom($form->get('nom')->getData());
            }
            if($form->get('telephone')->getData() != null){
                $user->setTelephone($form->get('telephone')->getData());
            }
            if($form->get('mail')->getData() != null){
                $user->setMail($form->get('mail')->getData());
            }
            if($form->get('password')->getData() != null && $form->get('password')->getData() != $user->getPseudo())
            $user->setPassword($userPasswordHasherInterface->hashPassword(
                $user,
                $form->get('password')->getData()
            ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('utilisateur');
        }
            return $this->render('utilisateur/modificationUtilisateur.html.twig', [
                'modificationUtilisateurForm' => $form->createView(),
            ]);
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/afficheutilisateur/{id}", name="affiche_utilisateur" , methods={"GET"})
     */
    public function AfficheUtilisateur(Participant $participant): Response
    {
        if($participant != null) {
            return $this->render('affiche_utilisateur/afficheUtilisateur.html.twig', [
                'participant' => $participant,
            ]);
        }
        else{
            return $this->redirectToRoute('sortie_index');
        }
    }
}
