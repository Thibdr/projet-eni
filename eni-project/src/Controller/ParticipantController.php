<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\ImportType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\ImportFactory;

/**
 * @Route("admin/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/", name="participant_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="participant_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/import", name="participant_import", methods={"GET","POST"})
     */
    public function import(Request $request, ImportFactory $iFactory): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csvFile */
            $csvFile = $form->get('donnees')->getData();

            $process = $iFactory->import($csvFile);

            $this->addFlash('import-success', 'Importation réussi :');
            $this->addFlash('import', $process['imported'].' utilisateur(s) ajouté(s)');
            if($process['!imported'] > 0){
                $this->addFlash('!import', $process['!imported'].' utilisateur(s) non ajouté(s)');
                $this->addFlash('!import', 'Le pseudo existe déjà, ou le nom du campus est incorrect');
            }

            return $this->render('import/import_files.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->render('import/import_files.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(ParticipantRepository $participantRepository,Request $request, Participant $participant, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $id = $participant->getId();
        $user = clone $participantRepository->find($id);
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo_user = $participantRepository->findExistPseudo($form->get('pseudo')->getData());
            if ($pseudo_user != null && $user->getPseudo() != $pseudo_user[0]['pseudo']) {
                $this->addFlash('error', 'Le pseudo est déjà utilisé, veuillez en choisir un autre');
                return $this->renderForm('participant/edit.html.twig', [
                    'participant' => $participant,
                    'form' => $form,
                ]);
            }
            if($form->get('pseudo')->getData() != $user->getPseudo() ){
                $user->setPseudo($form->get('pseudo')->getData());
            }
            if($form->get('password')->getData() != null && $userPasswordHasherInterface->hashPassword($user, $form->get('password')->getData()) != $userPasswordHasherInterface->hashPassword($user, $user->getPassword())) {
                $participant->setPassword($userPasswordHasherInterface->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                ));
            }
            else {
                $participant->setPassword($user->getPassword());
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="participant_delete", methods={"POST"})
     */
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
    }
}
