<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Production;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CatalogController extends AbstractController
{
    private $em = null;


    #[Route('/', name: 'app_catalog')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->em = $doctrine->getManager();

        $selectedCategories = $request->query->get('category');
        $selectedCategories = explode(',',$selectedCategories);

        if($selectedCategories[0]=="")
            unset($selectedCategories[0]);

        $searchField = $request->request->get('search_field');

        $categories = $this->retrieveAllCategories();

        $products = $this->retrieveAllProducts();
        if ($selectedCategories!=null || $searchField!=null) {
            $products = $this->retrieveProductsFromCategories($selectedCategories, $searchField);
        }
        
        return $this->render('catalog/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
            'searchField'=>$searchField,
            'selectedCategories'=>$selectedCategories
        ]);
    }
    #[Route('/product/{idProduct}', name: 'app_catalog_modal')]
    public function infoProduct($idProduct, Request $request, ManagerRegistry $doctrine){
        $this->em = $doctrine->getManager();

        $product = $this->em->getRepository(Product::class)->find($idProduct);
        

        return $this->render('catalog/product.modal.html.twig',["product"=>$product]);
    }
    private function retrieveAllProducts()
    {

        return $this->em->getRepository(Product::class)->findAll();

    }
    private function retrieveProductsFromCategories($selectedCategories, $searchField)
    {
        return $this->em->getRepository(Product::class)->findWithCategories($selectedCategories, $searchField);
    }   
    private function retrieveAllCategories()
    {
        return $this->em->getRepository(Category::class)->findAll();
    }
}