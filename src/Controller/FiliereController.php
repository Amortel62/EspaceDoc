<?php

namespace App\Controller;

use App\Entity\Filiere;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FiliereController extends AbstractController
{
    /**
     * @Route("/filiere_liste", name="filiere_liste")
     */
    public function liste()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Filiere::class);
        $listeFiliere = $repository->findAll();

        return $this->render('filiere/liste.html.twig',[
            'listeFiliere' =>$listeFiliere
        ]);
    }

    /**
     * @Route("/filiere_ajout", name="filiere_ajout")
     */
    public function ajout(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Filiere::class);
        $filiere = new Filiere();

        $form = $this->createFormBuilder($filiere)
            ->add('nom', TextType::class)
            ->add('save',SubmitType::class)
            ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($filiere);
                $em->flush();
            }
        }

        return $this->render('filiere/ajout.html.twig',[
            'form' =>$form->createView()
        ]);
    }
}
