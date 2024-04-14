<?php
namespace App\Service;

use App\Entity\Category;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Source;
use App\Entity\Categorie;

class SourceService {
    private $client;
    private $entityManager;
    private $apiKey;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager, string $apiKey) {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->apiKey = $apiKey;
    }

    public function fetchAndStoreSources() {
        $response = $this->client->request(
            'GET',
            'https://newsapi.org/v2/sources',
            [
                'query' => ['apiKey' => $this->apiKey, 'language' => 'fr']
            ]
        );

        $data = $response->toArray();

        foreach ($data['sources'] as $sourceData) {
            $source = $this->entityManager->getRepository(Source::class)->findOneBy(['name' => $sourceData['name']]);
            if (!$source) {
                $source = new Source();
                $source->setName($sourceData['name']);

                $categoryName = $sourceData['category'];
                $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
                if (!$category) {
                    $category = new Categorie();
                    $category->setName($categoryName);
                    $this->entityManager->persist($category);
                }
                $source->setCategory($category);
                $this->entityManager->persist($source);
            }
        }
        $this->entityManager->flush();
    }
}
