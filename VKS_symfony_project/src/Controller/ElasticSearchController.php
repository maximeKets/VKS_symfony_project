<?php

namespace App\Controller;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ElasticSearchController extends AbstractController
{
    private $finder;

    public function __construct(TransformedFinder $finder)
    {
        $this->finder = $finder;
    }
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request ): Response
    {
        $query = $request->query->get('query', '');
        $articles = $this->finder->find($query);
        return $this->render('article/query.html.twig', [
            'articles' => $articles,
        ]);
    }
}