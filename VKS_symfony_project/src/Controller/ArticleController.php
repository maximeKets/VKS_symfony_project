<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    private $articleRepository;
    private $paginator;
    private $entityManager;

    public function __construct(ArticleRepository $articleRepository, PaginatorInterface $paginator, EntityManagerInterface $entityManager)
    {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_article')]
    #[Route('/page}', name: 'app_article_paginated')]
    public function index(int $page = 1): Response
    {
        $pagination = $this->paginator->paginate(
            $this->articleRepository->createQueryBuilder('a')->getQuery(),
            $page,
            32
        );
        return $this->render('article/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/article/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();
        return $this->handleArticleForm($request, $article, 'Article créé avec succès !');
    }

    #[Route('/article/{article}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article): Response
    {
        return $this->handleArticleForm($request, $article, 'Article modifié avec succès !');
    }

    #[Route('/article/{article}', name: 'app_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', ['article' => $article]);
    }

    #[Route('/article/{article}/delete', name: 'app_article_delete')]
    public function delete(Article $article): Response
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        $this->addFlash(
            'notice',
            'Article supprimé avec succès !'
        );

        return $this->redirect($this->generateUrl('app_article'));
    }

    private function handleArticleForm(Request $request, Article $article, string $successMessage): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser()->getEmail());
            $article->setPublishedAt(new DateTime());
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('notice', $successMessage);

            return $this->redirectToRoute('app_article_show', ['article' => $article->getId()]);
        }
        return $this->render('article/edit.html.twig', [
            'form' => $form
        ]);
    }
}
