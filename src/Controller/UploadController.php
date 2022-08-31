<?php

namespace App\Controller;
use App\Form\ParticipantCsvType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{

    /**
     * @Route("/test-upload", name="app_test_upload")
     * @IsGranted("ROLE_USER")
     */
    public function excelCommunesAction(Request $request, FileUploader $file_uploader)
    {
        $form = $this->createForm(ParticipantCsvType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $form['upload_file']->getData();
            if ($file)
            {
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory.'/'.$file_name;
                    // Do what you want with the full path file...
                    // Why not read the content or parse it !!!

                }
/*                else
                {
                    // Oups, an error occured !!!
                }*/
            }
            return $this->redirectToRoute('app_participant_index');
        }
        return $this->render('upload/index.html.twig', [
            'formUpload' => $form->createView(),
        ]);
    }
    // ...
}