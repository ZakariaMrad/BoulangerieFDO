<?php

namespace App\Controller;

use App\Entity\Category;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private $em = null;

    #[Route('/page', name: 'app_contact')]
    public function index(Request $request, ManagerRegistry $doctrine): Response

    {
        $this->em = $doctrine->getManager();

        $categories = $this->retrieveAllCategories();

        return $this->render('page/index.html.twig', [
            'searchField' => "",
            'categories' => $categories,
            'selectedCategories'=>''
        ]);
    }
    private function retrieveAllCategories()
    {
        return $this->em->getRepository(Category::class)->findAll();
    }
}
