<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Service\OcrService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppController extends AbstractController
{
    #[Route('/upload', name: 'app')]
    public function upload(Request $request, SluggerInterface $slugger): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageDir = $this->getParameter('image_directory');

                try {
                    $imageFile->move($imageDir, $newFilename);
                } catch (FileException $e) {}

                $image->setName($newFilename);
            }
        }

        return $this->render('app/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'app')]
    public function testImageText(Request $request, OcrService $ocrService): Response
    {
        $imagePath = $this->getParameter('image_directory').'/talon_test_3.jpg';

        $text = $ocrService->extractTextFromImage($imagePath);

        dd($text);
    }
}
