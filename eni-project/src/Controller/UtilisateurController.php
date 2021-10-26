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
        $user = new Participant();
        $tableRepo = $this->getDoctrine()->getManager()->getRepository(Participant::class);
        $form = $this->createForm(ModificationUtilisateurType::class, $user);
        $form->handleRequest($request);
        $error = null;
        $success = null;

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $table = $tableRepo->findAll();

            for($i = 0; $i<count($table); $i++){
                if($user->getPseudo() != $form->get('pseudo')->getData() && $table[$i]->getPseudo() == $form->get('pseudo')->getData()) {
                    $error = "Le nom du pseudo est déjà existant";
                    return $this->render('utilisateur/modificationUtilisateur.html.twig', [
                        'modificationUtilisateurForm' => $form->createView(),
                        'success' => $success,
                        'error' => $error
                    ]);
                }
            }

            if($form->get('pseudo')->getData() != $user->getPseudo() ){
                $user->setPseudo($form->get('pseudo')->getData());
            }
            if($form->get('prenom')->getData() != $user->getPrenom() ){
                $user->setPrenom($form->get('prenom')->getData());
            }
            if($form->get('nom')->getData() != $user->getNom()){
                $user->setNom($form->get('nom')->getData());
            }
            if($form->get('telephone')->getData() != $user->getTelephone()){
                $user->setTelephone($form->get('telephone')->getData());
            }
            if($form->get('mail')->getData() != $user->getMail()){
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

                $nom = "../public/assets/images/".$name['photo'];
                move_uploaded_file($tmp['photo'], $nom);
                $nomsave = "assets/images/".$name['photo'];
                $user->setPhoto($nomsave);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $success = "L'utilisateur à bien été modifié !";

            return $this->render('utilisateur/modificationUtilisateur.html.twig', [
                'modificationUtilisateurForm' => $form->createView(),
                'success' => $success,
                'error' =>$error
            ]);
        }
            return $this->render('utilisateur/modificationUtilisateur.html.twig', [
                'modificationUtilisateurForm' => $form->createView(),
                'success' => $success,
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
