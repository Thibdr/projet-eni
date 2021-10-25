<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/villes")
 */
class VilleController extends AbstractController
{
    /**
     * @Route("/", name="ville_index", methods={"GET", "POST"})
     */
    public function index(Request $request, VilleRepository $villeRepository): Response
    {
        $ville = new Ville();

        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('ville_index', [], Response::HTTP_SEE_OTHER);
        }

        $filterForm = $this->getFilterForm();
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = (object) $filterForm->getData();
            $nom = $data->nom;

            if(!empty($nom)) {
                $villes = $villeRepository->findByName($nom);
            } else {
                $villes = $this->getAllCities($villeRepository);
            }
        } else {
            $villes = $this->getAllCities($villeRepository);
        }

        return $this->renderForm('ville/index.html.twig', [
            'villes' => $villes,
            'ville' => $ville,
            'form' => $form,
            'filterForm' => $filterForm,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="ville_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ville $ville): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ville_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="ville_delete", methods={"POST"})
     */
    public function delete(Request $request, Ville $ville): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ville_index', [], Response::HTTP_SEE_OTHER);
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
     * Return all cities
     *
     * @param VilleRepository $villeRepository
     * @return array
     */
    private function getAllCities(VilleRepository $villeRepository): array {
        return $villeRepository->findAll();
    }
}
