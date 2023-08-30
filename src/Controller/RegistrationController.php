<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Form\RegistrationFormType;
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


class RegistrationController extends AbstractController
{
    private $em = null;

    #[Route('/register', name: 'app_register')]
    public function register(Security $security, ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $this->em = $doctrine->getManager();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $errors = $form->getErrors(true);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );



            $entityManager->persist($user);
            $entityManager->flush();
            $security->login($user);



            return $this->redirectToRoute('app_catalog');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'errors' => $errors,
            'categories' => $this->retrieveAllCategories(),
            'selectedCategories' => '',
            'searchField' => ''
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(ManagerRegistry $doctrine, AuthenticationUtils $authenticationUtils): Response
    {
        $this->em = $doctrine->getManager();

        if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error != null && $error->getMessageKey() === 'Invalid credentials.') {
            $this->addFlash(
                "login",
                new Notification("Invalid credentials", NotificationColors::ERROR)
            );
        }

        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('registration/login.html.twig', [
            'last_email' => $lastEmail,
            'categories' => $this->retrieveAllCategories(),
            'selectedCategories' => '',
            'searchField' => ''
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {

        throw new \Exception("Don't forget to activate logout in security.yaml");
    }

    private function retrieveAllCategories()
    {
        return $this->em->getRepository(Category::class)->findAll();
    }
}
