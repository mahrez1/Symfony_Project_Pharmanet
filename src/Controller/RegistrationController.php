<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //token activation
            $user->setActivationToken(md5(uniqid()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $message = (new \Swift_Message('Activation de votre compte')) 
                ->setFrom('pidevjavatest@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        // templates/emails/registration.html.twig
                        'email/activation.html.twig',
                        ['token' => $user->getActivationToken()]
                    ),
                    'text/html'
                );
            $mailer->send($message);
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            ); 
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /** 
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['activation_token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('cet utilisateur n\'existe pas');
        } else {
            $user->setActivationToken(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('message', 'vous avez activer votre compte');
            return $this->redirect('/profil');
        }
    }

    /**
     * @Route("/user/create", name="createUser")
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $user->setEmail($request->get('email'));
        $user->setRoles([$request->get('role')]);
        $user->setUsername($request->get("username"));
        $user->setLastname($request->get("lastname"));
        $user->setTel("test");
        $user->setAdress("test");
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $request->get('password')
            )
        );

        $user->setActivationToken(md5(uniqid()));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);

        $entityManager->flush();

        $message = (new \Swift_Message('Confirmation de votre compte'))
            ->setFrom('pidevjavatest@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'email/activation.html.twig',
                    ['token' => $user->getActivationToken()]
                ),
                'text/html'
            );
        $mailer->send($message);

        return new Response("User created successfully!");
    }

    /**
     * @Route("/user/login", name="loginUser")
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['email' => $request->get('email')]);

        if ($user == null)
            return new Response("nouserfound");

        if ($passwordEncoder->isPasswordValid($user, $request->get('password'), null))
            return new Response("succeeded");
        else
            return new Response("failed");
    }

    /**
     * @Route("/user/getPasswordByEmail", name="getPasswordByEmail")
     */
    public function getPasswordByEmail(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer): Response
    {
        $mp = '123456';

        $message = (new \Swift_Message('Mot de passe oublié'))
            ->setFrom('pidevjavatest@gmail.com')
            ->setTo($request->get('email'))
            ->setBody(
                'Bienvenu sur Doctourna : Tapez ce mot de passse : '.$mp.' dane le champs requis et appuis sur confirmer.'
            );
        $mailer->send($message);

        return new Response("Password sent.");
    }
}
