<?php

namespace App\Command;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ModifEtatCommand extends Command
{
    protected static $defaultName = 'app:modif-etat';
    protected static $defaultDescription = 'Permet de mettre à jour l\'etat de la sortie';

    private $sortieRepository;


    public function __construct(SortieRepository $sortieRepository,EntityManagerInterface  $entityManager,EtatRepository $etatRepository)
    {
        parent::__construct();
        $this->sortieRepository = $sortieRepository;
        $this->entityManager = $entityManager;
        $this->etatRepository = $etatRepository;
    }


    protected function configure()
    {
        $this
            ->setName('demo:greet')
            ->setDescription('Greet someone')
        ;
    }



    protected function execute(InputInterface $input, OutputInterface $output):int{


       $Allsortie = $this->sortieRepository->findAll();
       $Alletat = $this->etatRepository->findAll();
       $maintenantErreur = new \DateTime;
       $maintenant = $maintenantErreur->modify("+120 minutes");
        //date_default_timezone_set('Europe/Paris');
       // $maintenant = new \DateTime();


        foreach ($Allsortie as $sortie){

        if ($maintenant > $sortie->getDateLimiteInscription()){

            $sortie->setEtat($Alletat[2]);
        }
        if($maintenant < $sortie->getDateLimiteInscription()) {

            $sortie->setEtat($Alletat[1]);
        }

            $dateHeureDebut = clone $sortie->getDateHeureDebut();
            $dateFinActivite = ($sortie->getDateHeureDebut()->modify('+' . $sortie->getDuree() . 'minutes'));


        if (($maintenant > $dateHeureDebut) and ($maintenant <= $dateFinActivite)){
            $sortie->setEtat($Alletat[3]);
            dump($maintenant);
            dump($dateHeureDebut);
        }
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();
        }
        return Command::SUCCESS;
    }

}
