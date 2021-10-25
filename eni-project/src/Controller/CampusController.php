<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/campus")
 */
class CampusController extends AbstractController
{
    /**
     * @Route("/", name="campus_index", methods={"GET", "POST"})
     */
    public function index(Request $request, CampusRepository $campusRepository): Response
    {
        $campus = new Campus();

        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('campus_index', [], Response::HTTP_SEE_OTHER);
        }

        $filterForm = $this->getFilterForm();
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = (object) $filterForm->getData();
            $nom = $data->nom;

            if(!empty($nom)) {
                $sites = $campusRepository->findByName($nom);
            } else {
                $sites = $this->getAllSites($campusRepository);
            }
        } else {
            $sites = $this->getAllSites($campusRepository);
        }

        return $this->renderForm('campus/index.html.twig', [
            'sites' => $sites,
            'campus' => $campus,
            'form' => $form,
            'filterForm' => $filterForm,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="campus_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Campus $campus): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="campus_delete", methods={"POST"})
     */
    public function delete(Request $request, Campus $campus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('campus_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Return filter form
     *
     * @return FormInterface
     */
    private function getFilterForm(): FormInterface {
        return $this->createFormBuilder()
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => ' form-control'
                ],
                'label' => 'Le nom contient : ',
                'required' => false
            ])->getForm();
    }

    /**
     * Return all sites
     *
     * @param CampusRepository $campusRepository
     * @return array
     */
    private function getAllSites(CampusRepository $campusRepository): array {
        return $campusRepository->findAll();
    }
}
