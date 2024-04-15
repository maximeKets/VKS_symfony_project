<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    private $finder;

    public function __construct(TransformedFinder $finder)
    {
        $this->finder = $finder;
    }

    #[Route('/', name: 'app_article')]
    #[Route('/page/{page}', name: 'app_article_paginated')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $articleRepository->createQueryBuilder('a')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $query = $request->query->get('query', '');
        $articles = $this->finder->find($query);
        return $this->render('article/query.html.twig', [
            'articles' => $articles,
        ]);
    }

}
