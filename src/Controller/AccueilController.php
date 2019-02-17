<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")   
     */
    public function index()
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
    
    /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        return $this->render('accueil/faq.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
    
     /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos()
    {
        return $this->render('accueil/apropos.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
    
     /**
     * @Route("/mentions", name="mentions")
     */
    public function mentions()
    {
        return $this->render('accueil/mentions.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
}
