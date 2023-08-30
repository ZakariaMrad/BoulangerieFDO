<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Cart;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Notification;
use App\Core\NotificationColors;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $em = null;
    private $cart;

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->initSession($request);
        $this->em = $doctrine->getManager();

        return $this->render('cart/index.html.twig', [
            'searchField' => "",
            'categories' => $this->retrieveAllCategories(),
            'selectedCategories' => '',
            'cart' => $this->cart
        ]);
    }


    #[Route('/cart/add/{idProduct}', name: 'purchase_add')]
    public function addPurchase($idProduct, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->initSession($request);
        $this->em = $doctrine->getManager();

        if ($this->cart->getIsLocked()) {
            $this->addFlash(
                "cart",
                new Notification("The cart cannot be modified for the moment", NotificationColors::ERROR)
            );
            return $this->redirectToRoute('app_cart');
        }
        $product = $this->retrieveProductById($idProduct);

        if ($product == null) {
            $this->addFlash(
                "cart",
                new Notification("This product cannot be added to cart", NotificationColors::ERROR)
            );
            return $this->redirectToRoute('app_cart');
        }


        $response = $this->cart->add($product);
        if ($response == "added") {
            $this->addFlash(
                "cart",
                new Notification("The product was added to cart!", NotificationColors::ADD)
            );
        } else {
            $this->addFlash(
                "cart",
                new Notification("The cart was updated!", NotificationColors::UPDATE)
            );
        }


        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/update', name: 'purchase_update', methods: ['POST'])]
    public function updatePurchase(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->initSession($request);
        $this->em = $doctrine->getManager();
        if ($this->cart->getIsLocked()) {
            $this->addFlash(
                "cart",
                new Notification("The cart cannot be modified for the moment", NotificationColors::ERROR)
            );
            return $this->redirectToRoute('app_cart');
        }
        $post = $request->request->all();
        if (is_numeric($post['inpQuantity']) || $post['inpQuantity'] < 1) {
            $this->addFlash(
                "cart",
                new Notification("Please do not do that", NotificationColors::ERROR)
            );
            return $this->redirectToRoute('app_cart');
        }

        $action = $request->request->get('action');

        if ($action == "update") {
            $this->cart->update($post);
            $this->addFlash(
                "cart",
                new Notification("The product was updated!", NotificationColors::UPDATE)
            );
        } else if ($action == "remove") {
            $session = $request->getSession();
            $session->remove('cart');
        }



        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{idPurchase}', name: 'purchase_remove')]
    public function removePurchase($idPurchase, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->initSession($request);
        $this->em = $doctrine->getManager();

        if ($this->cart->getIsLocked()) {
            $this->addFlash(
                "cart",
                new Notification("The cart cannot be modified for the moment", NotificationColors::ERROR)
            );
            return $this->redirectToRoute('app_cart');
        }

        $this->cart->removeOne($idPurchase);

        $this->addFlash(
            "cart",
            new Notification("The product was removed to cart!", NotificationColors::REMOVE)
        );


        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/review', name: 'cart_review')]
    public function reviewCart(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()) {
            $this->addFlash(
                "login",
                new Notification("Please connect to your account before ordering", NotificationColors::UPDATE)
            );
            return $this->redirectToRoute('app_login');
        }
        $this->initSession($request);
        $this->em = $doctrine->getManager();
        return $this->render('cart/review.html.twig', [
            'searchField' => "",
            'categories' => $this->retrieveAllCategories(),
            'selectedCategories' => '',
            'cart' => $this->cart
        ]);
    }

    private function initSession(Request $request)
    {

        $session = $request->getSession();

        $this->cart = $session->get('cart', new Cart());

        $session->set('cart', $this->cart);
    }



    private function retrieveAllCategories()
    {
        return $this->em->getRepository(Category::class)->findAll();
    }

    private function retrieveProductById($idProduct): ?Product
    {
        return $this->em->getRepository(Product::class)->find($idProduct);
    }
}
