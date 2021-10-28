<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulationSortieType;
use App\Form\FiltreSortieType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\PersistentCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LieuRepository;
use Symfony\Component\Routing\Exception\NoConfigurationException;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_index", methods={"GET","POST"})
     */
    public function index(SortieRepository $sortieRepository, LieuRepository $lr, Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(FiltreSortieType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $site = $data['site'];
            $nom = $data['nom'];
            $orga = $data['orga'];
            $inscrit = $data['inscrit'];
            $non_inscrit = $data['non_inscrit'];
            $passees = $data['passees'];
            $start = $data['start'];
            $end = $data['end'];

            $form = $this->createForm(FiltreSortieType::class);
            $form->handleRequest($request);
            return $this->renderForm('sortie/index.html.twig', [
                'sorties' => $sortieRepository->findWithFilters(
                    $site,$nom,$orga,$inscrit,$non_inscrit,$passees,$start,$end,$user
                ),
                'form' => $form,
            ]);
        }

        return $this->renderForm('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $nouveauLieu = $form->get('nomLieu')->getData();

            if($nouveauLieu != null) {
                $lieu = new Lieu();
                $lieu->setNom($nouveauLieu);
                $lieu->setVille($form->get('ville')->getData());
                $lieu->setRue($form->get('rue')->getData());
                $lieu->setLatitude($form->get('latitude')->getData());
                $lieu->setLongitude($form->get('longitude')->getData());
                $sortie->setLieu($lieu);
                $entityManager->persist($lieu);
            }

            $etat = ($form->getClickedButton()->getName() === 'save') ? 'En création' : 'Ouverte';
            $sortie->setEtat($etat);
            $user = $this->getUser();
            $sortie->setOrganisateur($user);
            $sortie->setCampus($user->getCampus());

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été créée');
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        if($sortie->getEtat() == 'Historisée') {
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        if($sortie->getEtat() != 'En création') {
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $nouveauLieu = $form->get('nomLieu')->getData();

            if($nouveauLieu != null) {
                $lieu = new Lieu();
                $lieu->setNom($nouveauLieu);
                $lieu->setVille($form->get('ville')->getData());
                $lieu->setRue($form->get('rue')->getData());
                $lieu->setLatitude($form->get('latitude')->getData());
                $lieu->setLongitude($form->get('longitude')->getData());
                $sortie->setLieu($lieu);
                $entityManager->persist($lieu);
            }

            $etat = ($form->getClickedButton()->getName() === 'save') ? 'En création' : 'Ouverte';
            $sortie->setEtat($etat);

            $entityManager->flush();

            $this->addFlash('success', "La sortie a été modifiée");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if($sortie->getEtat() != 'En création') {
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        $this->addFlash('success', "La sortie a été supprimée");
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/cancel", name="sortie_cancel", methods={"GET","POST"})
     */
    public function cancel(Request $request, Sortie $sortie): Response
    {
        $isAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        if(!$isAdmin && $this->getUser()->getId() != $sortie->getOrganisateur()->getId()) {
            $this->addFlash('error', "Vous n'avez pas le droit d'accéder à cette page");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        $date = new \DateTime('now');
        if($sortie->getEtat() == "En création" || $sortie->getEtat() == "Annulée" || $sortie->getDateHeureDebut() < $date) {
            $this->addFlash('error', "Vous ne pouvez pas annuler la sortie");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(AnnulationSortieType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = (object)$form->getData();
            $sortie->setInformations($data->motif);
            $sortie->setEtat('Annulée');

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La sortie a été annulée');
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/cancel.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/subscribe", name="sortie_subscribe", methods={"GET"})
     */
    public function subscribe(Sortie $sortie): Response {
        if($sortie->getOrganisateur()->getId() == $this->getUser()->getId()) {
            $this->addFlash('error', "Vous ne pouvez pas vous inscrire à votre sortie");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        $date = new \DateTime('today');
        if($date > $sortie->getDateLimiteInscription()) {
            $this->addFlash('error', "Il n'est plus possible de s'inscrire à cette sortie");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        if($sortie->getEtat() != 'Ouverte') {
            $this->addFlash('error', "Les inscriptions à la sortie ne sont pas ouvertes.");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        if($sortie->getParticipant()->count() == $sortie->getNbInscriptionsMax()) {
            $this->addFlash('error', "Il n'y a plus de places disponibles pour cette sortie.");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout de l'utilisateur à la liste des participants de la sortie
        $sortie->addParticipant($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Vous êtes inscrit à la sortie');
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/unsubscribe", name="sortie_unsubscribe", methods={"GET"})
     */
    public function unsubscribe(Sortie $sortie): Response {

        $participants = $sortie->getParticipant();
        if(!$this->isSubscriber($participants, $this->getUser())) {
            $this->addFlash('error', "Vous n'êtes pas inscrit à cette sortie.");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        $date = new \DateTime('now');
        if($sortie->getDateHeureDebut() < $date) {
            $this->addFlash('error', "La sortie a débutée, vous ne pouvez plus vous désinscrire.");
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        // Suppression de l'utilisateur de la liste des participants de la sortie
        $sortie->removeParticipant($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Vous êtes désinscrit de la sortie');
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Check if the participant subscribe at the sortie
     *
     * @param PersistentCollection $participants
     * @param Participant $user
     * @return bool
     */
    private function isSubscriber(PersistentCollection $participants, Participant $user): bool {
        foreach($participants as $participant) {
            if($participant->getId() == $user->getId()) {
                return true;
            }
        }

        return false;
    }
}
