<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime;

use App\Entity\Ville;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Participant;
use App\Entity\Etat;
use App\Entity\Campus;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Ville 
        $ville = [];
        for ($i = 0; $i < 5; $i++) {
            $ville[$i] = new Ville();
            $ville[$i]->setNom('Ville'.$i);
            $ville[$i]->setCodePostal('3700'.$i);
            $manager->persist($ville[$i]);
        }
        $manager->flush();

        // Lieu
        $lieu = [];
        for ($i = 0; $i < 5; $i++) {
            $lieu[$i] = new Lieu();
            $lieu[$i]->setNom('Ville'.$i);
            $lieu[$i]->setRue('3700'.$i);
            $lieu[$i]->setLatitude(47.394144);
            $lieu[$i]->setLongitude(0.68484);
            $lieu[$i]->setVille($ville[$i]);
            $manager->persist($lieu[$i]);
        }        
        $manager->flush();

        // Lieu
        $etat = [];
        $fake_etat = ['Créée','Ouverte','Clôturée','Activité en cours','Passée','Annulée'];
        for ($i = 0; $i < 6; $i++) {
            $etat[$i] = new Etat();
            $etat[$i]->setLibelle($fake_etat[$i]);
            $manager->persist($etat[$i]);
        }        
        $manager->flush();

        // Campus
        $campus = [];
        for ($i = 0; $i < 3; $i++) {
            $campus[$i] = new Campus();
            $campus[$i]->setNom('Campus'.$i);
            $manager->persist($campus[$i]);
        }        
        $manager->flush(); 
        
        // Participant
        $participant = [];
        for ($i = 0; $i < 30; $i++) {
            $participant[$i] = new Participant();
            $participant[$i]->setNom('NomParticipant'.$i);
            $participant[$i]->setPrenom('PrenomParticipant'.$i);
            $participant[$i]->setPseudo('PseudoParticipant'.$i);
            $participant[$i]->setTelephone('0712345678');
            $participant[$i]->setMail('participant'.$i.'@sortir.com');
            $participant[$i]->setPassword('SecretPassword');
            $participant[$i]->setActif(true);
            $participant[$i]->setCampus($campus[rand(0,2)]);
            $manager->persist($participant[$i]);
        }        
        $manager->flush();

        // Sortie
        $sortie = [];
        for ($i = 0; $i < 5; $i++) {
            $sortie[$i] = new Sortie();
            $sortie[$i]->setNom('Sortie'.$i);
            $sortie[$i]->setDateHeureDebut(new DateTime());
            $sortie[$i]->setDuree(30);
            $sortie[$i]->setDateLimiteInscription(new DateTime());
            $sortie[$i]->setNbInscriptionsMax(30);
            $sortie[$i]->setInfoSortie('Infos Sortie'.$i);
            $sortie[$i]->setEtat($etat[rand(0,5)]);
            $sortie[$i]->setCampus($campus[rand(0,2)]);
            $sortie[$i]->setLieu($lieu[rand(0,4)]);
            $sortie[$i]->setOrganisateur($participant[rand(0,29)]);
            for ($y = 0; $y < 30; $y++) {
                $sortie[$i]->addParticipant($participant[$y]);
            }
            $manager->persist($sortie[$i]);
        }        
        $manager->flush();
    }
}
