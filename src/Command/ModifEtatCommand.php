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
       $maintenant = new \DateTime();

        foreach ($Allsortie as $sortie){
            $dateHeureDebut = clone $sortie->getDateHeureDebut();
            $dateFinActivite = clone ($sortie->getDateHeureDebut()->modify('+' . $sortie->getDuree() . 'minutes'));
            $dateArchive = ($sortie->getDateHeureDebut()->modify('+ 30 days'));

        if ($maintenant > $sortie->getDateLimiteInscription() and $maintenant < $sortie->getDateHeureDebut()){
            $etatCloture = $this->etatRepository->findOneBy(['libelle' => Etat::ETAT_CLOTURE]);
            $sortie->setEtat($etatCloture);
        }

        if ($maintenant > $dateFinActivite ){
            $etatPassee = $this->etatRepository->findOneBy(['libelle' => Etat::ETAT_PASSEE]);
            $sortie->setEtat($etatPassee);
        }

        if($maintenant < $sortie->getDateLimiteInscription()) {
            $etatOuvert = $this->etatRepository->findOneBy(['libelle' => Etat::ETAT_OUVERT]);
            $sortie->setEtat($etatOuvert);
        }

        if (($maintenant > $dateHeureDebut) and ($maintenant <= $dateFinActivite)){
            $etatEnCours = $this->etatRepository->findOneBy(['libelle' => Etat::ETAT_EN_COURS]);
            $sortie->setEtat($etatEnCours);
        }

        if($maintenant > $dateArchive) {
            $etatArchivee = $this->etatRepository->findOneBy(['libelle' => Etat::ETAT_ARCHIVEE]);
            $sortie->setEtat($etatArchivee);
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
