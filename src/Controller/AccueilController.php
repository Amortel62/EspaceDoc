<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Filiere;
use App\Form\AccueilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccueilController extends AbstractController {

    /**
     * @Route("/redirectafterlogout", name="redirectafterlogout")
     */
    public function redirectafterlogout(Request $request) {
        $referer = $request->headers->get('referer'); //redirige vers la previous page
        return $this->redirect($referer);

    }

    /**
     * @Route({
    "fr" : "/accueil",
     *  "en" : "/home",
     *  "de" : "/willkommen",
     *  "es" : "/bienvenida"}, name="accueil")
     */
    public function index(TranslatorInterface $translator, $locales, $defaultLocale) {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route({
    "fr" : "/faq",
     *  "en" : "/faq_en",
     *  "de" : "/hgf",
     *  "es" : "/pf"}, name="faq")
     */
    public function faq() {
        return $this->render('accueil/faq.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route({
    "fr" : "/apropos",
     *  "en" : "/about",
     *  "de" : "/über",
     *  "es" : "/aproposito"}, name="apropos")
     */
    public function apropos() {
        return $this->render('accueil/apropos.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route({
    "fr" : "/mentions",
     *  "en" : "/notice",
     *  "de" : "/impressum",
     *  "es" : "/aviso"}, name="mentions")
     */
    public function mentions() {
        return $this->render('accueil/mentions.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route({
         "fr" : "/moncompte",
     *  "en" : "/myaccount",
     *  "de" : "/meinkonto",
     *  "es" : "/micuenta"}, name="moncompte")
     */
    public function moncompte(Request $request) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('nom', TextType::class, array(
                'required' => false
            ))
            ->add('prenom', TextType::class, array(
                'required' => false
            ))
            ->add('datenaissance', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false))
            ->add('dateinscription', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'disabled' => 'true'))
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
     * @Route({
    "fr" : "/inscrire",
     *  "en" : "/register",
     *  "de" : "/registrieren",
     *  "es" : "/registro"}, name="inscrire")
     */
    public function inscrire(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $user = new User();
        $form = $this->createForm(AccueilType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
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
/*
    /**
     * @Route("/departement-select", name="departement_select")
     */
/*
    public function getFiliereSelect(Request $request){
        $departement = new Departement();
        $departement->setNom($request->get('departement'));
        $form = $this->createForm(AccueilType::class, $departement);
        // no field? Return an empty response
        if (!$form->has('filiere')) {
            return new Response(null, 204);
        }
        return $this->render('accueil/filiere_name.html.twig', [
            'filiereForm' => $form->createView(),
        ]);
    }*/
}
