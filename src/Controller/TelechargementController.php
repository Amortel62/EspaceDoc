<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Telechargement;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class TelechargementController extends AbstractController
{
    /**
     * @Route("/telechargement_liste", name="telechargement_liste")
     */
    public function liste(Request $request)
    {
        
        $hasAccess = $this->isGranted('ROLE_ADMIN'); //Renvoie true si l'utilisateur connecté possède le rôle ADMIN

        $repository = $this->getDoctrine()->getManager()->getRepository(Telechargement::class); //On récupère les informations de la table Telechargement
        $telechargement = new Telechargement(); //On instancie une nouvelle instance de listeTelechargement

        $form = $this->createFormBuilder($telechargement)//On créé le formulaire
                ->add('save', SubmitType::class, array('attr' => array('class' => 'save'), 'label' => 'Supprimer'))//Bouton qui permet de supprimer dans la suite du code
                ->getForm(); //Finition
        if ($request->isMethod('POST')) {//On récupère les informations du formulaire quand il est envoyé
            $form->handleRequest($request);
            if ($form->isValid()) {//On vérifie qu'il est bien valide
                $cocher = $request->request->get('cocher'); //On récupère toutes les cases cochées
                foreach ($cocher as $i) {//Pour chaque case cochée
                    $u = $repository->find($i); //On récupère les informations liées à la case cochée
                    $this->getDoctrine()->getManager()->remove($u); //On supprime ces dernières
                }
                $this->getDoctrine()->getManager()->flush(); //On met à jour la BD
            }
        }
        if ($hasAccess == true) {//S'il l'utilisateur est bien ADMIN
            $listeTelechargements = $repository->findAll(); //On récupère la liste de tous les telechargement
            return $this->render('telechargement/liste.html.twig', [
                        'listeTelechargements' => $listeTelechargements, 'form' => $form->createView(),
            ]); //Affiche la page twig lié à ce controller et on transmet le formulaire
        }
    }
    
    
    /**
     * @Route("/download_telechargementlog", name="download_telechargementlog")
     */
    public function download_telechargementlog(){
              
        return $this->file('C:\xampp\htdocs\futur\logs\telechargement.log');
        
    }
    
}
