<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    #[Route('/galerie', name: 'app_gallery', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        PhotoRepository $photos,
        EntityManagerInterface $em
    ): Response {
        $uploadDir = $this->getParameter('gallery_dir');
        (new Filesystem())->mkdir($uploadDir);

        // --- Upload réservé aux admins ---
        if ($request->isMethod('POST')) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN'); // ⬅️ bloque si pas admin

            /** @var UploadedFile[]|null $files */
            $files = $request->files->get('photos');

            if ($files) {
                foreach ($files as $file) {
                    if (!$file instanceof UploadedFile) continue;

                    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!in_array($file->getMimeType(), $allowed, true)) {
                        $this->addFlash('danger', 'Format non supporté (JPG/PNG/WEBP).');
                        continue;
                    }
                    if ($file->getSize() > 10 * 1024 * 1024) {
                        $this->addFlash('danger', 'Fichier trop lourd (> 10MB).');
                        continue;
                    }

                    $ext = $file->guessExtension() ?: 'jpg';
                    $safeName = bin2hex(random_bytes(8)) . '.' . $ext;

                    try {
                        $file->move($uploadDir, $safeName);

                        $photo = (new Photo())
                            ->setFilename($safeName)
                            ->setOriginalName($file->getClientOriginalName());

                        $em->persist($photo);
                    } catch (\Throwable $e) {
                        $this->addFlash('danger', 'Erreur upload : ' . $e->getMessage());
                    }
                }
                $em->flush();
                $this->addFlash('success', 'Upload terminé.');
            }

            return $this->redirectToRoute('app_gallery');
        }

        // --- Liste : dernières publiées d'abord ---
        $list = $photos->createQueryBuilder('p')
            ->andWhere('p.isPublished = :pub')->setParameter('pub', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()->getResult();

        return $this->render('gallery/index.html.twig', [
            'photos' => $list,
        ]);
    }

    #[Route('/galerie/supprimer/{id}', name: 'app_gallery_delete', methods: ['POST'])]
    public function delete(
        Photo $photo,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // ⬅️ supprimer réservé admin

        if (!$this->isCsrfTokenValid('delete_photo_' . $photo->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_gallery');
        }

        $uploadDir = $this->getParameter('gallery_dir');
        $filepath = $uploadDir . DIRECTORY_SEPARATOR . $photo->getFilename();
        $fs = new Filesystem();
        try {
            if ($fs->exists($filepath)) {
                $fs->remove($filepath);
            }
        } catch (\Throwable $e) {
            // log si besoin
        }

        $em->remove($photo);
        $em->flush();

        $this->addFlash('success', 'Photo supprimée.');
        return $this->redirectToRoute('app_gallery');
    }
}
