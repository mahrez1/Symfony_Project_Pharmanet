<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Comment ;
use App\Entity\LikeComment;
use App\Entity\Dislikecomment;
use App\Form\RegisterLivreurType;
use App\Form\RegisterPharmacienType; 
use App\Repository\CommentRepository;
use App\Repository\DislikecommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="index")
     */
    public function index(): Response
    {
        if($this->getUser())
            $i=$this->getUser();
            $k = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($i);
        return $this->render('admin/board.html.twig', [
            'k' => $k,
        ]);
    } 
     /**
     * @Route("admin/show/users", name="usersshow")
     */
    public function showusers(): Response
    {
            $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll(); 
          
            return  $this->render('admin/users.html.twig',[
            'user' => $user,]); 
}
     /**
     * @Route("admin/user/delete/{id}", name="deleteuseraction")
     */
    public function deleteUser($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager ->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
    
        return $this->redirect('/admin/show/users'); 
        }

  //show list of comments to delete  fuction 

    /**
     * @Route("admin/delete/comments", name="deletecomments")
     */ 
    public function showcomment()
    {
        $comment = $this->getDoctrine()
        ->getRepository(Comment::class)
        ->findAll();
    
        return  $this->render('admin/comments.html.twig',[
        'comment' => $comment,]);
    }
     
      //delete a comment fuction

    /**
     * @Route("admin/comments/delete/{id}", name="deletecommentaction")
     */
    public function deletecomment($id)
    { 
            $entityManager = $this->getDoctrine()->getManager();
            $comment = $entityManager ->getRepository(Comment::class)->find($id);
            $entityManager->remove($comment);
            $entityManager->flush();
           
        return  $this->redirect('/admin/delete/comments'); 
}

        
      /**
     * @Route("admin/register/pharmacien", name="registrationPharmacien")
     */
    public function registrationPharmacien(Request $request, UserPasswordEncoderInterface $encoder)
    {
     
        $user =new User(); 
       
       $form = $this->createForm(RegisterPharmacienType::class,$user);  
      

       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
        $password = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setRoles(['ROLE_PHARMACIEN']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirect('/admin');
       }
       return $this->render('admin/registerPharmacien.html.twig',['form' => $form->createView()]);
    }
     /**
     * @Route("admin/register/livreur", name="registrationlivreur")
     */
    public function registrationlivreur(Request $request, UserPasswordEncoderInterface $encoder)
    {
        
        $user =new User(); 
       
       $form = $this->createForm(RegisterLivreurType::class,$user); 
      

       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
        $password = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setRoles(['ROLE_LIVREUR']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirect('/admin');
       }
       return $this->render('admin/registerlivreur.html.twig',['form' => $form->createView()]);
    }


   
   /**
     * @Route("admin/site/info", name="infosite")
     */
    public function infosite() 
    { 
        
        $em = $this->getDoctrine()->getManager();

        $repou = $em->getRepository(User::class);
        $totalusers = $repou->createQueryBuilder('a')
           
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();


        
        $repoc = $em->getRepository(Comment::class);
        $totalcomment = $repoc->createQueryBuilder('a')
          
            ->select('count(a.id)') 
            ->getQuery()
            ->getSingleScalarResult();
 



            $repod = $em->getRepository(Dislikecomment::class);
            $totaldislikes = $repod->createQueryBuilder('a')
              
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();



                $repol = $em->getRepository(LikeComment::class);
                $totallikes = $repol->createQueryBuilder('a')
                   
                    ->select('count(a.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
       
      

           
    return  $this->render('/admin/info.html.twig',[
    'totalcomment' => $totalcomment,'totalusers' => $totalusers,'totallikes' => $totallikes,'totaldislikes' => $totaldislikes]); 
    


}


}