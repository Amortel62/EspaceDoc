<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Fichier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ThemeController extends AbstractController {

    /**
     * @Route({
        "fr" : "/theme_ajout",
     *  "en" : "/theme_add",
     *  "de" : "/thema_hinzufügen",
     *  "es" : "/tema_agregar"}, name="theme_ajout")
     */
    public function ajout(Request $request) {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $theme = new Theme();
        $form = $this->createFormBuilder($theme)
                ->add('libelle', TextType::class)
                ->add('save', SubmitType::class, array('label' => 'Ajouter'))
                ->add('filiere', EntityType::class,array(
                    'class' => 'App\Entity\Filiere',
                    'choice_label' => 'nom',
                ))
                ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($theme);
                $em->flush();
            }
        }
        return $this->render('theme/ajout.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route({
        "fr" : "/theme_liste",
     *  "en" : "/theme_list",
     *  "de" : "/thema_liste",
     *  "es" : "/tema_lista"}, name="theme_liste")
     */
    public function liste(Request $request) {

         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getManager()->getRepository(Theme::class);
        $theme = new Theme();
        $form = $this->createFormBuilder($theme)
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
        $listeThemes = $repository->findAll();

        return $this->render('theme/liste.html.twig', [
                    'listeThemes' => $listeThemes, 'form' =>$form->createView(),

        ]);
    }

    /**
     * @Route({
        "fr" : "/theme_modifier/{id}",
     *  "en" : "/theme_edit/{id}",
     *  "de" : "/thema_bearbeiten/{id}",
     *  "es" : "/tema_editar/{id}"}, name="theme_modifier")
     */
    public function modifier(Request $request) {
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getManager()->getRepository(Theme::class);

        $theme = $repository->find($request->get('id'));
        $form = $this->createFormBuilder($theme)
                ->add('libelle', TextType::class)
                 ->add('filiere', EntityType::class,array(
                    'class' => 'App\Entity\Filiere',
                    'choice_label' => 'nom',
                ))
                ->add('save', SubmitType::class, array('label' => 'Modifier'))
                ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($theme);
                $em->flush();
            }
        }
        return $this->render('theme/modifier.html.twig', array(
                    'form' => $form->createView(),
        ));
    }
    /**
    * @Route("/theme_fichiers_liste/{id}", name="theme_fichiers_liste")
    */
    public function fichiersByThemeList(Request $request){

        $repository = $this->getDoctrine()->getManager()->getRepository(Theme::class);//On récupère les thèmes
        $repository2 = $this->getDoctrine()->getManager()->getRepository(Fichier::class);//On récupère les fichiers

        $theme = $repository->find($request->get('id'));//On récupère le thème cliqué

        /* $fichiersTheme = $repository2->findBy(['themes'=> $theme->getId()]);*/ //Le findBy sur une entité en relation Many-to-Many ne semble pas fonctionné

        $fichiersTheme = $repository2->findByTheme($theme);//Je récupère les fichiers du thèmes via un querybuilder dans FichierRepository

        
        return $this->render('theme/theme_fichiers_liste.html.twig',[
            'listeFichiersTheme' =>$fichiersTheme,
            'theme' =>$theme
        ]);
                   
    }
}
