<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;

use App\Form\FiltreSortieType;
use App\Form\CreationSortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\PersistentCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

        $form = $this->createForm(CreationSortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat('En création');
            $user = $this->getUser();
            $sortie->setOrganisateur($user);
            $sortie->setCampus($user->getCampus());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('notice', 'La sortie est créée');
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(CreationSortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/cancel", name="sortie_cancel", methods={"GET","POST"})
     */
    public function cancel(Request $request, Sortie $sortie): Response
    {
        if($this->getUser()->getId() != $sortie->getOrganisateur()->getId()) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit d'accéder à cette page", new NoConfigurationException());
        }
        if($sortie->getEtat() == 'Annulee') {
            throw $this->createAccessDeniedException('La sortie est déjà annulée', new NoConfigurationException());
        }

        $form = $this->createFormBuilder()->add('motif', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Motif',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
            ])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = (object)$form->getData();
            $sortie->setInformations($data->motif);
            $sortie->setEtat('Annulee');

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notice', 'La sortie a été annulée');
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
    public function subscribe(Request $request, Sortie $sortie): Response {
        if($sortie->getOrganisateur()->getId() == $this->getUser()->getId()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas vous inscrire à votre sortie", new NoConfigurationException());
        }

        $date = new \DateTime('today');
        if($date > $sortie->getDateLimiteInscription()) {
            throw $this->createAccessDeniedException("Il n'est plus possible de s'inscrire à cette sortie", new NoConfigurationException());
        }

        if($sortie->getEtat() != 'Ouverte') {
            throw $this->createAccessDeniedException("Les inscriptions à la sortie ne sont pas ouvertes.", new NoConfigurationException());
        }

        if($sortie->getParticipant()->count() == $sortie->getNbInscriptionsMax()) {
            throw $this->createAccessDeniedException("Il n'y a plus de places disponibles pour cette sortie.", new NoConfigurationException());
        }

        // Ajout de l'utilisateur à la liste des participants de la sortie
        $sortie->addParticipant($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', 'Vous êtes inscrit à la sortie');
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/unsubscribe", name="sortie_unsubscribe", methods={"GET"})
     */
    public function unsubscribe(Request $request, Sortie $sortie): Response {

        $participants = $sortie->getParticipant();
        if(!$this->isSubscriber($participants, $this->getUser())) {
            throw $this->createAccessDeniedException("Vous n'êtes pas inscrit à cette sortie.", new NoConfigurationException());
        }

        $date = new \DateTime('now');
        if($sortie->getDateHeureDebut() < $date) {
            throw $this->createAccessDeniedException("La sortie a débutée, vous ne pouvez plus vous désinscrire.", new NoConfigurationException());
        }

        // Suppression de l'utilisateur de la liste des participants de la sortie
        $sortie->removeParticipant($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', 'Vous êtes désinscrit de la sortie');
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
