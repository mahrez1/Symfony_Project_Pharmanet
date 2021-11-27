<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Produit;
use App\Form\CommentType;
use App\Entity\LikeComment;
use App\Entity\Dislikecomment;
use App\Form\ModifyCommentType;

use Doctrine\Persistence\ObjectManager;

use App\Repository\LikeCommentRepository; 
use App\Repository\DislikecommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{

  /**
     * @Route("/profil/produit/{id}/show", name="showcomment")
     */
    public function show($id){
        $mp = $this->getDoctrine()
        ->getRepository(Produit::class)
        ->find($id);
        
        return $this->render('comment/index.html.twig', [
            'mp' => $mp,
        ]);}
       
 
   /**
     * @Route("/profil/comment", name="addcommenttop") 
     */
    public function addcomment(Request $request ){
        if ($this->getUser()){
        $id=$request->request->get('idproduit') ; 
        $comment = new Comment();
        $comment->setContent($request->request->get('content'));
        $comment->setCreationdate(new \DateTime(date("Y-m-d H:i:s")));
        $comment->setUser($this->getUser());
        $comment->setProduit($this->getDoctrine()->getRepository(Produit::class)->find($id));
      
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        
        $entityManager->flush();
    
    }
    return  new Response("le commantaire est ajoute");
    
        //return  new Response("le commantaire est ajouter");
        //return $this->redirect('/profil/produit/{{id}/show');
}
 //delete a comment for a respected user fuction

    /**
     * @Route("profil/comments/user/delete/{id}", name="deletecommentactionuser")
     */
    public function deletecomment($id )
    {
       if ($this->getUser()){  
        $i= $this->getDoctrine()
        ->getRepository(Comment::class)
        ->find($id)->getUser();
        if($this->getUser()== $i){

        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager ->getRepository(Comment::class)->find($id);
                
        $entityManager->remove($comment);
        $entityManager->flush();
        return  new Response("le commentaire est supprime");

        }
       else
       return  new Response("c'est pas la tienne");
    }
    }
    /**
     * @Route("/profil/Modify/comment/{id}", name="Modifiercomment")
     */ 
    public function ModifiercommentAction(Request $request, $id)
    { if($this->getUser()){
        $i= $this->getDoctrine()
        ->getRepository(Comment::class)
        ->find($id)->getUser();
        if($this->getUser()== $i){
            $entityManager = $this->getDoctrine()->getManager();
            $c= $entityManager ->getRepository(Comment::class)->find($id);  
        //dump($c);die() ;
        $form = $this->createFormBuilder($c)
        
        ->add('content', TextType::class, array(
            'attr' => array('class' => 'form-control'),
        ))
        ->getForm();
   
        $form->handleRequest($request);
        
     if($form->isSubmitted()&& $form->isValid()){
         $data = $form->getData();
        
         $c->setcontent($data->getContent());
         $entityManager=$this->getDoctrine()->getManager();
         $entityManager->persist($c);
         $entityManager->flush();
         $mp = $this->getDoctrine()
        ->getRepository(Comment::class)
        ->find($id)->getProduit()->getId();

        return  new Response("le commentaire est modifier");
       
         
     }
    
    }
    else
       return  new Response("c'est pas la tienne");
     return $this->render('comment/modifycomment.html.twig', array('form' => $form->createView())); 
    }

}

 /**
     * @Route("/profil/comment/like/{id}", name="likecomment")
     * @param \App\Repository\LikeCommentRepository $LikeRep
     * @param \App\Entity\Comment $post
     * @param \Doctrine\Persistence\ObjectManager $manager
     * @param \Symfony\Component\HttpFoundation\Response
     */ 
    public function like(Comment $comment, ObjectManager $manager ,LikeCommentRepository $likeRep): Response
    {  
     
     $user= $this->getUser() ;
     if($comment->isLikedByUser($user)){ 

        $like=$likeRep->findOneBy([ 
        'comment'=>$comment,
         'user' =>$user 
        ]);
      
        return $this->json([
            'code' => 200,
            'message' =>'like est supprimer' ,
            'likes' =>$likeRep->count(['comment' => $comment])


        ], 200);
     }
     else{

        $like= new LikeComment( ) ;
 $like->setComment($comment)
    ->setUser($user) ;
    $manager->persist($like) ;
    $manager->flush() ;

    return $this->json([
        'code' => 200,
        'message' =>'c bon' ,
        'likes' =>$likeRep->count(['comment' => $comment])
    ], 200) ;
     }
 
    

}

/**
     * @Route("/profil/comment/dislike/{id}", name="dislikecomment")
     * @param \App\Repository\DislikecommentRepository $LikeRep
     * @param \App\Entity\Comment $post
     * @param \Doctrine\Persistence\ObjectManager $manager
     * @param \Symfony\Component\HttpFoundation\Response
     */ 
    public function dislike(Comment $comment, ObjectManager $manager ,DislikecommentRepository $likeRep): Response
    {  
     
     $user= $this->getUser() ;
     if($comment->isdisLikedByUser($user)){

        $like=$likeRep->findOneBy([ 
        'comment'=>$comment,
         'user' =>$user
        ]);
        
        return $this->json([
            'code' => 200,
            'message' =>'like est supprimer' ,
            'dislikes' =>$likeRep->count(['comment' => $comment])


        ], 200);
     }
     else  {

        $like= new Dislikecomment( ) ;
 $like->setComment($comment)
    ->setUser($user) ;
    $manager->persist($like) ;
    $manager->flush() ;

    return $this->json([
        'code' => 200,
        'message' =>'c bon' ,
        'dislikes' =>$likeRep->count(['comment' => $comment])
    ], 200) ;

   
     }
   
       
  
    
}




}
