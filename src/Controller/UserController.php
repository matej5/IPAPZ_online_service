<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class UserController
 * @package App\Controller
 *
 * Security annotation on login will throw 403 and on register route we use redirect to route. Both examples are correct.
 */
class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @Security("not is_granted('ROLE_USER')")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param EntityManagerInterface $entityManager
     * @return null|Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    ) {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            //todo insert worker and other roles
//            $a=['ROLE_BOSS'];
//            $a=['ROLE_WORKER'];
//            $user->setRoles($a);
            $user->setMoney(200);
            $user->createAvatar();
            $user->setImage('avatar.jpeg');
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile", name="app_profile")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return null|Response
     */
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->setFirstname($form->get('firstname')->getData());
            $this->getUser()->setLastname($form->get('lastname')->getData());
            $entityManager->flush();
        }

        return $this->render('user/view.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/generate", name="app_generate")
     * @param Request $request
     * @return null|Response
     */
    public function generate(
        Request $request
    ) {
        $user = $this->getUser();
        $user->createAvatar();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        return $this->render('user/view.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }



    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }
}
