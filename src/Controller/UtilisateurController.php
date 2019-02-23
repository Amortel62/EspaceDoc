<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Knp\Component\Pager\PaginatorInterface;


class UtilisateurController extends AbstractController {

    /**
     * @Route("/utilisateur_ajout", name="utilisateur_ajout")
     */
    public function ajout(Request $request) {

        $utilisateur = new User();
        $form = $this->createFormBuilder($utilisateur)
                ->add('username', TextType::class)
                ->add('password', PasswordType::class)
                ->add('nom', TextType::class)
                ->add('prenom', TextType::class)
                ->add('datenaissance', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ))              
                ->add('roles', ChoiceType::class, array(
                    'mapped' => false, 'choices' => array(
                        'Utilisateur' => 'ROLE_USER',
                        'Administrateur' => 'ROLE_ADMIN'
                    ),
                ))
                ->add('save', SubmitType::class, array('label' => 'Ajouter'))
                ->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $utilisateur->setDateinscription(new \DateTime);
                $utilisateur->setRoles(array($request->get('form')['roles']));
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

        $repository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $utilisateur = new User();
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
                    'listeUtilisateurs' => $listeUtilisateurs, 'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/utilisateur_modifier/{id}", name="utilisateur_modifier")
     */
    public function modifier(Request $request) {
        
        
        $repository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $utilisateur = $repository->find($request->get('id'));

        $role = $utilisateur->getRoles();//CES DEUX LIGNES PEUVENT ETRE AMELIOREES JE PENSE 
        $selected = $role[0];
    
      
        $form = $this->createFormBuilder($utilisateur)
                ->add('username', TextType::class)
                ->add('nom', TextType::class)
                ->add('prenom', TextType::class)
                ->add('datenaissance', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',))
                ->add('dateinscription', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'disabled' => 'true'))
                ->add('roles', ChoiceType::class, array(
                    'mapped' => false, 'choices' => array(
                        'Utilisateur' => 'ROLE_USER',
                        'Administrateur' => 'ROLE_ADMIN'
                    ),
                    'data' => $selected,
                ))
                ->add('save', SubmitType::class, array('label' => 'Modifier'))
                ->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $utilisateur->setRoles(array($request->get('form')['roles']));
                $em = $this->getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->flush();
            }
        }
        return $this->render('utilisateur/modifier.html.twig', ['form' => $form->createView()]);
    }

}
