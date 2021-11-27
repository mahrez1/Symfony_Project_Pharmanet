<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route;
 
class UserController extends AbstractController
{
     
       /**
     * @Route("/", name="page")
     */
    public function showfrontpage(): Response
    {
        return $this->render('user/index.html.twig', [ 
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/profil", name="app_homepage")
     */
    public function index(): Response
    {   if($this->getUser()){ 
        return $this->render('user/frontpage.html.twig', [
            'controller_name' => 'UserController', 
        ]);}
    }
     /**
     * @Route("/profil/info", name="infoprofil")
     */
    public function info(): Response
    { if($this->getUser()){
        $i=$this->getUser();
        $k = $this->getDoctrine()
        ->getRepository(User::class)
        ->find($i);
       // dump($mp);die() ;
        return $this->render('user/profil.html.twig', [
            'k' => $k,
        ]);}
    } 
    /**
     * @Route("/livreur", name="livreur")
     */
    public function indexlivreur(): Response
    {
        return $this->render('livreur/livreurfrontpage.html.twig', [
            'controller_name' => 'UserController',
        ]); 
    }
    /**
     * @Route("/pharmacien", name="pharmacien")
     */
    public function indexpharmacien(): Response 
    {
        return $this->render('pharmacien/pharmacienfrontpage.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
