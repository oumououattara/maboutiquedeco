<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterController
 * @package App\Controller
 */
class RegisterController extends AbstractController
{
    /**
     * Doctrine
     *
     * @var [type]
     */
    private $entityManager;

    /**
     * RegisterController constrcutor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *  @Route("/register", name="register")
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user_email = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if (!$user_email) {
                $password = $hasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('notice', 'Success');
            } else {
                $this->addFlash('notice', 'Email already use.');
            }
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
