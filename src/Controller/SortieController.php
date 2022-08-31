<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\FiltreSortieType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="app_sortie_index")
     * @IsGranted("ROLE_USER")
     */
    public function index(ParticipantRepository $participantRepository, SortieRepository $sortieRepository, CampusRepository $campusRepository, Request $request): Response
    {

        $form = $this->createForm(FiltreSortieType::class);
        $tabRequest = $request->get("filtre_sortie");
        $isCheck = false;
        $listeInscrit = [];
        $listeSortiePassee = [];
        $listeNonInscrit = [];
        $filterByDate = [];

        if ($tabRequest == null) {
            return $this->render('sortie/index.html.twig', [
                'sorties' => $sortieRepository->findAll(), 'form' => $form->createView(), 'isCheck' => $isCheck,
                'listeInscrit' => $listeInscrit, 'listeSortiePassee' => $listeSortiePassee, 'listeNonInscrit' => $listeNonInscrit, 'filterByDate' => $filterByDate
            ]);
        } else {
            $sortie = $sortieRepository->find($tabRequest["nomSortie"]);
            $campus = $campusRepository->find($tabRequest["campus"]);
            if (isset($tabRequest['public'])) {
                if (($tabRequest['public'][0] == "1")) {
                    $isCheck = true;
                }
                if ($tabRequest['public'][0] == "2") {
                    $listeInscrit = $sortieRepository->findBy(["participant" => $this->getUser()]);
                }
                if ($tabRequest['public'][0] == "3") {
                    $listeNonInscrit = $sortieRepository->findNonInscrit($this->getUser());
                }
                if ($tabRequest['public'][0] == "4") {
                    $listeSortiePassee = $sortieRepository->findBy(["etat" => "5"]);
                }
            }
            if (isset($tabRequest['dateDebut'])) {
                $beginDate = $tabRequest['dateDebut'];
                $endDate = $tabRequest['dateFin'];
                $filterByDate = $sortieRepository->findByDate($beginDate, $endDate);
            }
            $sorties = $sortieRepository->findByField($sortie, $campus);
            return $this->renderForm('sortie/index.html.twig',
                compact('sorties', 'form', 'isCheck', 'listeInscrit', 'listeSortiePassee', 'listeNonInscrit', 'filterByDate'));
        }


    }

    /**
     * @Route("/new", name="app_sortie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $etat = new Etat();
        $etat = $etatRepository->find(2);
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setParticipant($this->getUser());
            $sortie->setEtat($etat);
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'participants' => $sortie->getParticipants(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_sortie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_sortie_delete_page", methods={"GET"})
     */
    public function deletePage(Sortie $sortie): Response
    {
        return $this->render('sortie/delete.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    /**
     * @Route("/{id}", name="app_sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
            $this->addFlash('success', 'Cette sortie a bien été supprimer pour la raison suivante : ' . $request->request->get('motif', "défaut"));

        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/organisateur/{pseudo}", name="app_participant_show_pseudo", methods={"GET"})
     */
    public function showByPseudo(
        string                $pseudo,
        Participant           $participant,
        ParticipantRepository $participantRepository
    ): Response
    {
        $participant = $participantRepository->findOneBy((["pseudo" => $pseudo]));
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * *@Route ("/inscrit/{sortie}", name="app_inscription")
     */
    public function inscription(
        Sortie                 $sortie,
        ParticipantRepository  $participantRepository,
        EntityManagerInterface $entityManager
    )
    {
        $mail = $this->getUser()->getUserIdentifier();
        $moimeme = $participantRepository->findOneBy(["email" => $mail]);


        if ($sortie->getEtat()->getLibelle() === 'Ouverte') {
            $sortie->addParticipant($moimeme);
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index');
        }

        return $this->render('sortie/index.html.twig');
    }

    /**
     * *@Route ("/desinscrit/{sortie}", name="app_desinscription")
     */
    public function desinscription(
        Sortie                 $sortie,
        ParticipantRepository  $participantRepository,
        EntityManagerInterface $entityManager
    )
    {
        $mail = $this->getUser()->getUserIdentifier();
        $moimeme = $participantRepository->findOneBy(["email" => $mail]);


        $sortie->removeParticipant($moimeme);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('app_sortie_index');
    }

}

