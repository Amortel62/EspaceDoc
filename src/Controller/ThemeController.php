<?php

namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ThemeController extends AbstractController {

    /**
     * @Route("/theme_ajout", name="theme_ajout")
     */
    public function ajout(Request $request) {
        $theme = new Theme();
        $form = $this->createFormBuilder($theme)
                ->add('libelle', TextType::class)
                ->add('save', SubmitType::class, array('label' => 'Ajouter'))
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
     * @Route("/theme_liste", name="theme_liste")
     */
    public function liste(Request $request) {

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
     * @Route("/theme_modifier{id}", name="theme_modifier")
     */
    public function modifier(Request $request) {

        $repository = $this->getDoctrine()->getManager()->getRepository(Theme::class);

        $theme = $repository->find($request->get('id'));
        $form = $this->createFormBuilder($theme)
                ->add('libelle', TextType::class)
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
        return $this->render('theme/ajout.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

}
