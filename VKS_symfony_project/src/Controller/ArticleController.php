<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use FOS\ElasticaBundle\Finder\TransformedFinder;
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
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/search/{query}', name: 'app_search')]
    public function search(string $query): Response
    {
        $articles = $this->finder->find($query);
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
