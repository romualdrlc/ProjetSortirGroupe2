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

        $OUVERT = $Alletat[1];
        $CLOTUREE = $Alletat[2];
        $ENCOURS = $Alletat[3];
        $PASSEE = $Alletat[4];
        $ANNULEE = $Alletat[5];
        $ARCHIVEE = $Alletat[6];

        foreach ($Allsortie as $sortie){

            $dateHeureDebut = clone $sortie->getDateHeureDebut();
            $dateFinActivite = clone ($sortie->getDateHeureDebut()->modify('+' . $sortie->getDuree() . 'minutes'));
            $dateArchive = ($sortie->getDateHeureDebut()->modify('+ 30 days'));

        if ($maintenant > $sortie->getDateLimiteInscription() and $maintenant < $sortie->getDateHeureDebut()){

            $sortie->setEtat($CLOTUREE);
        }

        if ($maintenant > $dateFinActivite ){

            $sortie->setEtat($PASSEE);
        }

        if($maintenant < $sortie->getDateLimiteInscription()) {

            $sortie->setEtat($OUVERT);
        }

        if (($maintenant > $dateHeureDebut) and ($maintenant <= $dateFinActivite)){
            $sortie->setEtat($ENCOURS);
        }

        if($maintenant > $dateArchive)
        {
        $sortie->setEtat($ARCHIVEE);
        }

     /*   if($sortie->getEtat()->getId() == 6) {
            $sortie->setEtat($ANNULEE);
        }*/

        $this->entityManager->persist($sortie);
        $this->entityManager->flush();
        }
        return Command::SUCCESS;
    }

}
