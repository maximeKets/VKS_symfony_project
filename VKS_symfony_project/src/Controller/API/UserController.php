<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{


    #[Route('/api/user', name: 'api_user')]
    #[isGranted('ROLE_USER')]
    public function me()
    {
        return $this->json($this->getUser()
    );
    }
}