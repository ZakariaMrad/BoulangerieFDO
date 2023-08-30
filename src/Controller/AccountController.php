<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Order;
use App\Form\ModifyUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Core\Notification;
use App\Core\NotificationColors;
use App\Form\ModifyUserPasswordType;

class AccountController extends AbstractController
{
    private $em = null;

    #[Route('/account', name: 'app_account')]
    public function index(Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->em = $doctrine->getManager();

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_register');
        }

        $user = $this->getUser();



        $formUser = $this->createForm(ModifyUserFormType::class, $user);
        $formUser->handleRequest($request);
        $errorsUser = $formUser->getErrors(true);


        $formPassword = $this->createForm(ModifyUserPasswordType::class, $user);
        $formPassword->handleRequest($request);
        $errorsPassword = $formPassword->getErrors(true);


        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            if ($userPasswordHasher->isPasswordValid($user, $formPassword->get("old_password")->getData())) {
                if ($formPassword->get("old_password")->getData() == $formPassword->get('new_password')->getData()) {
                    $this->addFlash(
                        "password",
                        new Notification("Old password cannot be the same as new password", NotificationColors::ERROR)
                    );
                } else {

                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $formPassword->get('new_password')->getData()
                        )
                    );
                }
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_account');

        }

        return $this->render('account/index.html.twig', [
            'modifyUserForm' => $formUser->createView(),
            'errorsUsers' => $errorsUser,
            'modifyPasswordForm' => $formPassword->createView(),
            'errorsPassword' => $errorsPassword,
            'user' => $user,
            'categories' => $this->retrieveAllCategories(),
            'selectedCategories' => '',
            'searchField' => ''
        ]);
    }

    #[Route('/account/orders', name: 'account_orders')]
    public function reviewCart(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->em = $doctrine->getManager();
        $user = $this->getUser();
       
        return $this->render('account/orders.html.twig', [
            'searchField' => "",
            'categories' => $this->retrieveAllCategories(),
            'selectedCategories' => '',
            'currentOrders' =>$user->getCurrentOrders(),
            'pastOrders' =>$user->getPastOrders(),
        ]);
    }

    #[Route('/account/orders/{idOrder}', name: 'account_order')]
    public function removePurchase($idOrder, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->em = $doctrine->getManager();
        $user = $this->getUser();

        $order= $this->retrieveOneOrder($idOrder);
        if($order == null){
            return $this->redirectToRoute('account_orders');
        }
        // dd($order);
        return $this->render('account/order.html.twig', [
            'searchField' => "",
            'categories' => $this->retrieveAllCategories(),
            'selectedCategories' => '',
            'order'=> $order
        ]);
    }


    private function retrieveAllCategories()
    {
        return $this->em->getRepository(Category::class)->findAll();
    }
    private function retrieveOneOrder($idOrder)
    {
        return $this->em->getRepository(Order::class)->find($idOrder);
    }
}