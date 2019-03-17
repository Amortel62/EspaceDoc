<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Form\AccueilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccueilController extends AbstractController {

    private function generateUniqueFileName() {
        return md5(uniqid());
    }

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
     *     "fr" : "/contact",}, name="contact")
     */
    public function contact(\Swift_Mailer $mailer,Request $request){

        $form= $this->createFormBuilder()
            ->add('mail',EmailType::class,[
                'required' => true
            ])
            ->add('objet',TextType::class,[
                'required' => false
            ])
            ->add('demande',TextareaType::class,[
                'required' => true
            ])
            ->add('save',SubmitType::class,[
                'label' => 'Envoyer'
            ])
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $message = (new \Swift_Message($form->get('objet')->getData()))
                    ->setFrom($form->get('mail')->getData())
                    ->setTo('testmailingdevsymfony@gmail.com')
                    ->setBody(
                        $this->renderView(
                        'emails/messageContact.html.twig',
                        ['demande' => $form->get('demande')->getData(),
                        'sujet' => $form->get('objet')->getData()]
                    ),
                        'text/html')
                ;
                $accuseReception = (new \Swift_Message('Accusé de réception'))
                    ->setFrom('testmailingdevsymfony@gmail.com')
                    ->setTo($form->get('mail')->getData())
                    ->setBody(  $this->renderView(
                        'emails/accuse.html.twig',
                        ['demande' => $form->get('demande')->getData(),
                            'sujet' => $form->get('objet')->getData()]
                    ),
                        'text/html')
                ;

                $mailer->send($message);
                $mailer->send($accuseReception);
            }
        }
        return $this->render('accueil/contact.html.twig',[
            'form' => $form->createView(),
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
            ->add('imageFile',FileType::class,[
                'mapped' => false,
                'label' => 'Choisir une image de profil',
                'required' => false,
                'constraints' => new Image([
                    'maxSize' => '150k',
                ])
            ])
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
            if ($form->isSubmitted() && $form->isValid()) {

                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $form['imageFile']->getData(); //On récupère les données de l'image dans l'input file du form

                if($uploadedFile){ //Si ce qu'on récupère est truthy

                    $oldFileName = $this->getUser()->getImageFileName(); //On récupère le nom de l'image de profile de l'utilisateur
                    $folder =  $this->getParameter('image_directory'); //On récupère le chemin d'upload des images de profiles

                    if($oldFileName){ //Si l'utilisateur a déjà une image de profil
                        $oldFile = $folder .'/'. $oldFileName ; //On récupère le chemin de celle-ci
                        unlink($oldFile); //On l'efface
                    }

                    $newFilename = $this->generateUniqueFileName() .'.'. $uploadedFile->guessExtension();

                    $uploadedFile->move(
                        $folder,
                        $newFilename
                    );

                    $user->setImageFileName($newFilename);

                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        //A améliorer
        $repository  = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        $fichiers = $repository->findBy(['user' => $this->getUser()]); //On récupère les fichiers liés à l'utilisateur en cours
        $nombreDeFichiersUploads = count($fichiers);

        return $this->render('accueil/moncompte.html.twig', [
            'nb'   => $nombreDeFichiersUploads,
            'form' => $form->createView()]);
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
