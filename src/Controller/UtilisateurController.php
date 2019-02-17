<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UtilisateurController extends AbstractController {

    /**
     * @Route("/utilisateur_ajout", name="utilisateur_ajout")
     */
    public function ajout(Request $request) {

        $utilisateur = new Utilisateur();
        $form = $this->createFormBuilder($utilisateur)
                ->add('nom', TextType::class)
                ->add('prenom', TextType::class)
                ->add('datenaissance', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ))
                ->add('dateinscription', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ))
                ->add('save', SubmitType::class, array('label' => 'Ajouter'))
                ->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->flush();
            }
        }
        return $this->render('utilisateur/ajout.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/utilisateur_liste", name="utilisateur_liste")
     */
    public function liste(Request $request) {

        $repository = $this->getDoctrine()->getManager()->getRepository(Utilisateur::class);
        $utilisateur = new Utilisateur();
        $form = $this->createFormBuilder($utilisateur)
                ->add('save', SubmitType::class, array('attr' => array('class' => 'save'), 'label' => 'Supprimer'))
                ->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $cocher = $request->request->get('cocher');
                foreach ($cocher as $i) {
                    $u = $repository->find($i);
                    $this->getDoctrine()->getManager()->remove($u);
                }
                $this->getDoctrine()->getManager()->flush();
            }
        }
        $listeUtilisateurs = $repository->findAll();

        return $this->render('utilisateur/liste.html.twig', [
                    'listeUtilisateurs' => $listeUtilisateurs, 'form' =>$form->createView(),
        ]);
    }

    /**
     * @Route("/utilisateur_modifier/{id}", name="utilisateur_modifier")
     */
    public function modifier(Request $request) {
        $repository = $this->getDoctrine()->getManager()->getRepository(Utilisateur::class);
        $utilisateur = $repository->find($request->get('id'));
        $form = $this->createFormBuilder($utilisateur)
                ->add('nom', TextType::class)
                ->add('prenom', TextType::class)
                ->add('datenaissance', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',))
                ->add('dateinscription', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',))
                ->add('save', SubmitType::class, array('label' => 'Modifier'))
                ->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->flush();
            }
        }
        return $this->render('utilisateur/modifier.html.twig', ['form' => $form->createView()]);
    }

}
