<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    #[Route('/documents', name: 'app_documents')]
    public function index(PaginatorInterface $paginator, Request $request, EntityManagerInterface $em): Response
    {
        $query = $em->getRepository(Document::class)->createQueryBuilder('d')->getQuery();
        $documents = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('documents/index.html.twig', [
            'documents' => $documents,
        ]);
    }

    #[Route('/documents/new', name: 'app_documents_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentFormType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute('app_documents');
        }

        return $this->render('documents/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}