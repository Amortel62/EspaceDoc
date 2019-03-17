<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UtilisateurController extends AbstractController {

    /**
     * @Route({
        "fr" : "/utilisateur_ajout",
     *  "en" : "/user_add",
     *  "de" : "/nutzer_hinzufügen",
     *  "es" : "/usuario_agregar"}, name="utilisateur_ajout")
     */
    public function ajout(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $utilisateur = new User();
        $form = $this->createFormBuilder($utilisateur)
                ->add('username', TextType::class)
                ->add('password', PasswordType::class)
                ->add('nom', TextType::class)
                ->add('prenom', TextType::class)
                ->add('filiere', EntityType::class,[
                    'class' => 'App\Entity\Filiere',
                    'required' => true,
                    'choice_label' => 'nom'
                ])
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
                $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
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
     * @Route({
        "fr" : "/utilisateur_liste",
     *  "en" : "/user_list",
     *  "de" : "/nutzer_liste",
     *  "es" : "/usuario_lista"}, name="utilisateur_liste")
     */
    public function liste(Request $request) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
     * @Route({
        "fr" : "/utilisateur_modifier/{id}",
     *  "en" : "/user_edit/{id}",
     *  "de" : "/nutzer_bearbeiten/{id}",
     *  "es" : "/usuario_editar/{id}"}, name="utilisateur_modifier")
     */
    public function modifier(Request $request) {
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
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
                ->add('filiere',EntityType::class,[
                    'class' => 'App\Entity\Filiere',
                    'choice_label' => 'nom',
                    'required' => false,
                ])
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
        return $this->render('utilisateur/modifier.html.twig', ['form' => $form->createView(),'utilisateur' => $utilisateur]);
    }

}
