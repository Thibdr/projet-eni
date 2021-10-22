<?php

namespace App\Factory;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use App\Repository\CampusRepository;

/**
 * Cette classe permet d'importer un fichier CSV
 */
class ImportFactory 
{
    private $em; 
    private $uphi;
    private $cRepository;
    private $pRepository;
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasherInterface, ParticipantRepository $pRepository, CampusRepository $cRepository)
    {
        $this->em = $em;
        $this->uphi = $userPasswordHasherInterface;
        $this->cRepository = $cRepository;
        $this->pRepository = $pRepository;
    }

    function read($csv){
        $file = fopen($csv, 'r');
        while (!feof($file) ) {
            $line[] = fgetcsv($file, 1024);
        }
        fclose($file);
        return $line;
    }

    function import($csv){
        $data = $this->read($csv);
        $counter = ['imported' => 0, '!imported' => 0];
        foreach ($data as $line) {
            if(!$this->pRepository->findByPseudo($line[0])){
                $p = new Participant();
                $p->setPseudo($line[0])
                ->setRoles(array($line[1]));
                $p->setPassword($this->uphi->hashPassword(
                    $p,
                    'Pa$$w0rd'
                ))
                ->setNom($line[2])
                ->setPrenom($line[3])
                ->setTelephone($line[4])
                ->setMail($line[5])
                ->setActif(true);
                $campus = $this->cRepository->findByNom($line[6]);
                $p->setCampus($campus[0]);
    
                $this->em->persist($p);
                $counter['imported']++;
            }else{
                $counter['!imported']++;
            }
        }
        $this->em->flush();

        return $counter;
    }
}
