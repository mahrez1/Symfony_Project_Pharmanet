<?php

namespace App\Controller; 
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Form\ModifyProfileType ; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */ 
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //  return $this->redirectToRoute('/');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError(); 
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

 /** 
     * @Route("profil/changepassword", name="changepassword")
     */
    public function editAction(Request $request, UserPasswordEncoderInterface $encoder) 
    { if($this->getUser()){
        $user = $this->getUser(); 
        //dump($user->getEmail());die();
       $form = $this->createForm(ResetPasswordType::class,$user); 
       $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid()){
        $data = $form->getData();
        $password= $data->getPassword();
        $oldPassword= $data->getOldPassword();  
        $pass = $encoder->encodePassword($user,$password);
        $oldPass = $encoder->encodePassword($user,$oldPassword);
        $user->setPassword($pass);
        $user->setOldPassword($oldPass);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();  
        return $this->redirect('/profil');
    }
    return $this->render('user/changepassword.html.twig', array('form' => $form->createView())); 

        }}
          /**
     * @Route("/profil/Modify", name="ModifierProfile")
     */
    public function ModifierProfilAction(Request $request)
    { if($this->getUser()){
        $user = $this->getUser(); 

        $form = $this->createFormBuilder($user)
        
        ->add('tel', TextType::class, array(
            'attr' => array('class' => 'form-control'),
        ))
        ->add('adress', TextType::class, array(
            'attr' => array('class' => 'form-control'),
        ))
        
        ->getForm();
   
        $form->handleRequest($request);
        
     if($form->isSubmitted()&& $form->isValid()){
         $data = $form->getData();
         $user->setTel($data->getTel());
         $user->setAdress($data->getAdress());
         $entityManager=$this->getDoctrine()->getManager();
         $entityManager->persist($user);
         $entityManager->flush();
         return $this->redirect('/profil');
         
     }
     return $this->render('user/modifierprofil.html.twig', array('form' => $form->createView())); 
    }}

}
