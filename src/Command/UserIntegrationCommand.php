<?php

namespace App\Command;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserIntegrationCommand extends Command
{

    protected static $defaultName = 'app:UserIntegration';
    protected static $defaultDescription = 'Intégration de participant';
    private string $csv; // La route du fichier csv est importée grâce au fichier services.yaml
    private UserPasswordHasherInterface $hashedPassword;
    private EntityManagerInterface $manager;

    public function finder()
    {
        $finder = new Finder();
        $absoluteFilePath = null;

        $finder->files()->in('public/asset/csv');

        if ($finder->hasResults()){
            foreach ($finder as $file) {
                $absoluteFilePath = $file->getRealPath();
            }
        }

        return $absoluteFilePath;

    }

    /**
     * ImportCommand constructor.
     * @param string $csv
     * @param UserPasswordHasherInterface $hashedPassword
     * @param EntityManagerInterface $manager
     */
    public function __construct(UserPasswordHasherInterface $hashedPassword, EntityManagerInterface $manager)
    {
        parent::__construct(self::$defaultName);
        $this->csv = $this->finder();
        $this->hashedPassword = $hashedPassword;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this->setHelp('Permet l\'intégration de participants par un fichier csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (($handle = fopen($this->csv, "r")) !== false) {
            while (($line = fgetcsv($handle, 1000, ";")) !== false) {
                $participant = new Participant();
                $participant
                    ->setNom($line[0])
                    ->setPrenom($line[1])
                    ->setEmail($line[2])
                    ->setRoles((array)'ROLE_USER')
                    ->setPseudo($line[3])
                    ->setActif(true)
                    ->setUpdatedAt(new \DateTime());

                $participant->setPassword($this->hashedPassword->hashPassword($participant, "password"));

                $this->manager->persist($participant);

            }

            unlink($this->csv);

            fclose($handle);
        }
        $this->manager->flush();
        $io->success('Votre commande est un franc succès');
        return Command::SUCCESS;

    }

}
