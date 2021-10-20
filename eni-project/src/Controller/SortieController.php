<?php

namespace App\Controller;

use App\Entity\Sortie;

use App\Form\SortieType;
use App\Form\FiltreSortieType;
use App\Form\CreationSortieType;
use App\Repository\SortieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\LieuRepository;

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


            is_null($site) ? $sorted = $sortieRepository->findAll(): $sorted = $sortieRepository->findBySite($site) ;

            $form = $this->createForm(FiltreSortieType::class);
            $form->handleRequest($request);
            return $this->renderForm('sortie/index.html.twig', [
                'sorties' => $sorted,
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
     * @Route("/{id}", name="sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}
