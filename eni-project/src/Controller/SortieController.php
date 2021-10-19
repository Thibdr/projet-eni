<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_index", methods={"GET"})
     */
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(VilleRepository $villesRepo, EtatRepository $etatRepo, Request $request): Response
    {
        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);
        //$form = $this->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*$data = (object)$form->getData();

            $lieu = new Lieu();
            $lieu->setNom($data->libelle);
            $lieu->setRue($data->rue);
            $lieu->setLatitude($data->latitude);
            $lieu->setLongitude($data->longitude);
            $villeId = $data->ville;
            $lieu->setVille($villesRepo->find($villeId));

            $sortie->setNom($data->nom);
            $sortie->setDateHeureDebut($data->dateHeureDebut);
            $sortie->setDateLimiteInscription($data->dateLimiteInscription);
            $sortie->setNbInscriptionsMax($data->nbInscriptionsMax);
            $sortie->setDuree($data->duree);
            $sortie->setInfoSortie($data->infoSortie);
            $sortie->setLieu($lieu);*/
            $etat = $etatRepo->find(1);
            $sortie->setEtat($etat);
            $user = $this->getUser();
            $sortie->setOrganisateur($user);

            $entityManager = $this->getDoctrine()->getManager();
            //$entityManager->persist($lieu);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('notice', 'La sortie est créee');
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            //'villes' => $villesRepo->findAll()
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
        $form = $this->createForm(SortieType::class, $sortie);
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

    private function getForm() {
        return $this->createFormBuilder()
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => "Date limite d'inscription",
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' =>  'Nombre de places'
            ])
            ->add('duree', IntegerType::class, [
                'label' =>  'Durée'
            ])
            ->add('infoSortie', TextareaType::class, [
                'label' => 'Description et infos'
            ])
            ->add('ville', TextType::class)
            ->add('libelle', TextType::class)
            ->add('rue', TextType::class)
            ->add('latitude', TextType::class)
            ->add('longitude', TextType::class)
            ->getForm();
    }
}
