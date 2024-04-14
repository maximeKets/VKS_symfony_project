<?php

namespace App\Service;

use App\Entity\Category;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Entity\Source;

class NewsApiService
{
    private $client;
    private $entityManager;
    private $apiKey;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager, string $apiKey)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->apiKey = $apiKey;
    }

    public function fetchTopHeadlines()
    {
        $response = $this->client->request(
            'GET',
            'https://newsapi.org/v2/everything',
            [
                'query' => [
                    'q' => 'canada',
                    'language' => 'fr',
                    'apiKey' => $this->apiKey
                ]
            ]
        );

        $data = $response->toArray();
        foreach ($data['articles'] as $articleData) {
            $this->saveArticle($articleData);
        }
    }

    private function saveArticle(array $articleData)
    {
        $sourceName = $articleData['source']['name'];
        $source = $this->entityManager->getRepository(Source::class)->findOneBy(['name' => $sourceName]);
        if (!$source) {
            $source = new Source();
            $source->setName($sourceName);
            $source->setCategory($this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'General']));
            $this->entityManager->persist($source);
        }
        $article = new Article();
        $article->setTitle($articleData['title'] ?? '');
        $article->setAuthor($articleData['author'] ?? 'inconnu');
        $article->setUrlToImage($articleData['urlToImage'] ?? 'https://unsplash.com/fr/photos/un-tas-de-titres-de-nouvelles-rouges-sur-fond-rouge-MoGnx6Qq6m8');
        $article->setContent($articleData['content'] ?? '');
        $article->setPublishedAt(new \DateTime($articleData['publishedAt'] ?? 'now'));
        $article->setSource($source);

        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }
}
