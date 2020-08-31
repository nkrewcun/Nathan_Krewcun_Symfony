<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin", name="article_admin", methods={"GET"})
     */
    public function admin(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/admin.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/article/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setPublicationDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $picture = $form->get('picture')->getData();
            $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $picture->guessExtension();
            $picture->move(
                $this->getParameter('images_directory'),
                $newFilename
            );
            $article->setPicture($newFilename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'errors' => $form->getErrors()
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $originalPicture = $article->getPicture();
        $article->setPicture(
            new File($this->getParameter('images_directory').'/'.$article->getPicture())
        );
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();
            if ($picture) {
                $picture->move(
                    $this->getParameter('images_directory'),
                    $originalPicture
                );
            }
            $article->setPicture($originalPicture);
            $article->setLastModificationDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'originalPicture' => $originalPicture,
            'form' => $form->createView(),
            'errors' => $form->getErrors()
        ]);
    }

    /**
     * @Route("/article/{id}/delete", name="article_delete")
     */
    public function delete(Request $request, Article $article): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('article_index');
    }
}
