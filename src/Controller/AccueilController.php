<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccueilController extends AbstractController {

    /**
     * @Route("/accueil", name="accueil")   
     */
    public function index() {
        return $this->render('accueil/index.html.twig', [
                    'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faq() {
        return $this->render('accueil/faq.html.twig', [
                    'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos() {
        return $this->render('accueil/apropos.html.twig', [
                    'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route("/mentions", name="mentions")
     */
    public function mentions() {
        return $this->render('accueil/mentions.html.twig', [
                    'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route("/moncompte", name="moncompte")
     */
    public function moncompte(Request $request) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $this->getUser();
            $form = $this->createFormBuilder($user)
                ->add('username', TextType::class)
                ->add('nom', TextType::class)
                ->add('prenom', TextType::class)
                ->add('datenaissance', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',))
                ->add('save', SubmitType::class, array('label' => 'Modifier'))
                ->getForm();
             if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }
        }
        
        return $this->render('accueil/moncompte.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/inscrire", name="inscrire")
     */
    public function inscrire(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $user = new User();
        $form = $this->createFormBuilder($user)
                ->add('username', TextType::class)
                ->add('password', PasswordType::class)
                ->add('save', SubmitType::class, array('label' => 'S\'inscrire'))
                ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->setRoles(array('ROLE_USER'));
                $user->setDateinscription(new \DateTime());
                $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('accueil');
            }
        }

        return $this->render('accueil/inscrire.html.twig', ['form' => $form->createView()]);
    }

}
