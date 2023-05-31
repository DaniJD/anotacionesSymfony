<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Annotation;

class AnnotationController extends AbstractController
{
    public function index(Request $request): Response
    {
        $annotations = [];
        $storageDirectory = $this->getParameter('kernel.project_dir') . '/storage';

        if ($request->isMethod('POST')) {
            $annotation = new Annotation();
            $annotation->setTitle($request->request->get('title'));
            $annotation->setContent($request->request->get('content'));

            $filename = $storageDirectory . '/' . $annotation->getTitle() . '.txt';
            file_put_contents($filename, $annotation->getContent());

            return $this->redirectToRoute('annotation_index');
        } else {
            $files = scandir($storageDirectory);
            foreach ($files as $file) {
                if (is_file($storageDirectory . '/' . $file)) {
                    $title = pathinfo($file, PATHINFO_FILENAME);
                    $content = file_get_contents($storageDirectory . '/' . $file);
                    $annotation = new Annotation();
                    $annotation->setTitle($title);
                    $annotation->setContent($content);
                    $annotations[] = $annotation;
                }
            }
        }

        return $this->render('annotation/index.html.twig', [
            'annotations' => $annotations,
        ]);
    }
}
