<?php

namespace App\Controller;

use App\Core\Notification;
use App\Core\NotificationColors;
use App\Entity\Cart;
use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    private Cart $cart;
    private $em = null;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    #[Route('/stripe-checkout', name: 'stripe_checkout')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //Nous sommes connectés
        $user = $this->getUser();
        $this->initSession($request);
        $this->cart->setIsLocked(true);
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        $sessionData = [
            'line_items' => [[
                'quantity' => 1,
                'price_data' => ['unit_amount' => $this->cart->getTotalPriceStripe(), 'currency' => 'CAD', 'product_data' => ['name' => 'Microtransaction CSTJ']]
            ]],
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . "?stripe_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => $this->generateUrl('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ];

        $checkoutSession = \Stripe\Checkout\Session::create($sessionData);
        return $this->redirect($checkoutSession->url, 303);
    }

    #[Route('/stripe-success', name: 'stripe_success')]
    public function stripeSuccess(Request $request): Response
    {

        //Dans le TP 
        //Créer un commande
        //Transformer le panier en commande
        //MaJ des Quantité des produits
        //Vider le panier

        //Nous avons un paiement
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $this->initSession($request);
        try {

            //TODO: Valider que le paiement ait vraiment fonctionné chez stripe.
            //\Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
            $stripe = new \Stripe\StripeClient($_ENV["STRIPE_SECRET"]);

            $stripeSessionId = $request->query->get('stripe_id');
            $sessionStripe = $stripe->checkout->sessions->retrieve($stripeSessionId);
            $paymentIntent = $sessionStripe->payment_intent;
            $order = new Order($user, $paymentIntent, $this->cart);
            //paymentIntent sera à sauvegarder en BD


            foreach ($this->cart->getPurchases() as $purchase) {
                $mergedPurchase = $this->em->merge($purchase);
                $order->addPurchase($mergedPurchase);
            }

            $this->em->persist($order);
            $this->em->flush();
            $this->cart->empty();
            $this->cart->setIsLocked(false);
        } catch (\Exception $e) {
            //TODO : Redirection
        }

        return $this->redirectToRoute('account_orders');
    }

    #[Route('/stripe-cancel', name: 'stripe_cancel')]
    public function stripeCancel(Request $request): Response
    {
        $this->initSession($request);

        $this->cart->setIsLocked(false);
        $this->addFlash(
            "cart",
            new Notification("The payment was canceled 😢", NotificationColors::ERROR)
        );
        return $this->redirectToRoute('cart_review');
    }

    private function initSession(Request $request)
    {

        $session = $request->getSession();

        $this->cart = $session->get('cart', new Cart());

        $session->set('cart', $this->cart);
    }
}
