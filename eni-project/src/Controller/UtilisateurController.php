<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModificationUtilisateurType;
use App\Repository\ParticipantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/modificationUtilisateur", name="modification_utilisateur")
     */
    public function Update(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        if($this->getUser() != null) {
            $error ="";
        $user = new Participant();
        $tableRepo = $this->getDoctrine()->getManager()->getRepository(Participant::class);
        $form = $this->createForm(ModificationUtilisateurType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $table = $tableRepo->findAll();
            for($i = 0; $i<count($table); $i++){
                if($user->getPseudo() != $form->get('pseudo')->getData() && $table[$i]->getPseudo() == $form->get('pseudo')->getData()) {
                    $error = "Le nom du pseudo est déjà existant";
                    return $this->render('utilisateur/modificationUtilisateur.html.twig', [
                        'modificationUtilisateurForm' => $form->createView(),
                        'error' => $error,
                    ]);
                }
            }

            if($form->get('pseudo')->getData() != null && $form->get('pseudo')->getData() != $user->getPseudo() ){
                $user->setPseudo($form->get('pseudo')->getData());
            }
            if($form->get('prenom')->getData() != null && $form->get('prenom')->getData() != $user->getPrenom() ){
                $user->setPrenom($form->get('prenom')->getData());
            }
            if($form->get('nom')->getData() != null && $form->get('nom')->getData() != $user->getNom()){
                $user->setNom($form->get('nom')->getData());
            }
            if($form->get('telephone')->getData() != null && $form->get('telephone')->getData() != $user->getTelephone()){
                $user->setTelephone($form->get('telephone')->getData());
            }
            if($form->get('mail')->getData() != null && $form->get('mail')->getData() != $user->getMail()){
                $user->setMail($form->get('mail')->getData());
            }
            if($form->get('password')->getData() != null && $userPasswordHasherInterface->hashPassword($user, $form->get('password')->getData()) != $user->getPassword()) {
                $user->setPassword($userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                ));
            }
            if($form->get('photo')->getData() != null && $_FILES[$form->getName('photo')]['name'] != $user->getPhoto()) {
                $name = $_FILES[$form->getName('photo')]['name'];
                $tmp = $_FILES[$form->getName('photo')]['tmp_name'];

                $nom = "../public/assets/".$name['photo'];
                move_uploaded_file($tmp['photo'], $nom);
                $user->setPhoto($nom);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('modification_utilisateur');
        }
            return $this->render('utilisateur/modificationUtilisateur.html.twig', [
                'modificationUtilisateurForm' => $form->createView(),
                'error' => $error,
            ]);
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/afficheutilisateur/{id}", name="affiche_utilisateur" , methods={"GET"})
     */
    public function Show(Participant $participant): Response
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
