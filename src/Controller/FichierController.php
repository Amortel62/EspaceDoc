<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Entity\Telechargement;
use App\Repository\FichierRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class FichierController extends AbstractController {

    /**
     * @return string
     */
    private function generateUniqueFileName() {
        return md5(uniqid());
    }

    private function ecrire_log($errtxt) {
        $fp = fopen($this->getParameter('logs_directory').'\telechargement.log', 'a+'); // ouvrir le fichier ou le créer

        fseek($fp, SEEK_END); // poser le point de lecture à la fin du fichier
        $nouverr = $errtxt . "\r\n"; // ajouter un retour à la ligne au fichier
        fputs($fp, $nouverr); // ecrire ce texte
        fclose($fp); //fermer le fichier
    }

    /**
     * @Route("/get_file/{id}", name="get_file")
     */
    public function get_file(Request $request) {

        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        $fichier = $repository->find($request->get('id')); // On récupère le fichier grâce à l'id passé dans l'URL
        $hasAccess = $this->isGranted('ROLE_ADMIN'); //J'instancie une variable pour vérifier plus tard si le rôle de l'utilisateur est bien admin

        if ($fichier->getUser()->getId() == $this->getUser()->getId() || $hasAccess) {//Je vérifie si l'ID de l'utilisateur du fichier correspponds à l'ID de l'utilisateur connecté ou si l'utilisateur connecté est administrateur            
            $repository2 = $this->getDoctrine()->getManager()->getRepository(Telechargement::class);
            $leTelechargement = $repository2->findOneBy([
                'user' => $this->getUser(),
                'fichier' => $fichier
            ]); //Je récupère les téléchargements liés à l'utilisateur connecté et au fichier que l'on veut télécharger.
            if ($leTelechargement == null) {//S'il n'existe pas encore de téléchargement
                $leTelechargement = new Telechargement(); //J'instancie alors un nouveau Téléchargement.
            }
            $leTelechargement->setFichier($fichier); //Dans tous les cas, on set le fichier du téléchargement
            $leTelechargement->setUser($this->getUser()); //L'utilisateur qui a téléchargé.
            $leTelechargement->setNb($leTelechargement->getNb() + 1); //On récupère le nombre de téléchargement auquel on incrémente 1.
            $em = $this->getDoctrine()->getManager();
            $em->persist($leTelechargement);
            $em->flush(); //On met à jour la BD

            $this->ecrire_log('L\'utilisateur ' . $this->getUser()->getUsername() .
                    ' a téléchargé le fichier ' . $fichier->getNom() .
                    ' dont le propriétaire est ' . $fichier->getUser()->getUsername() .
                    ' C\'est la ' . $leTelechargement->getNb() .
                    ' fois qu\'il le télécharge'); //J'écris un log des téléchargements


            return $this->file($this->getParameter('file_directory') . '/' . $fichier->getNom()); //On télécharge le fichier.
        } else {

            return $this->maliste($request); //Sinon on renvoit à sa liste des fichiers.               
        }
    }

    /**
     * @Route({
        "fr" : "/fichier_ajout",
     *  "en" : "/file_add",
     *  "de" : "/datei_hinzufügen",
     *  "es" : "/archivo_agregar"}, name="fichier_ajout")
     */
    public function ajout(Request $request) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $filiere = $this->getUser()->getFiliere()->getId();

        $hasAccess = $this->isGranted('ROLE_ADMIN'); //Cette variable nous aide à savoir si l'utilisateur est administrateur
        $fichier = new Fichier();
        
    

        if ($hasAccess) {//Si l'utilisateur connecté est administrateur.        
            $form = $this->createFormBuilder($fichier)//On créé un formulaire                  
                    ->add('user', EntityType::class, array(
                        'class' => 'App\Entity\User',
                        'choice_label' => 'nom',
                    ))//Type entité permet de charger des "options" d'une entité doctrine, ici User. Donc on aura un select (html) avec les options de la table User
                    ->add('themes', EntityType::class, array(
                        'class' => 'App\Entity\Theme',
                        'choice_label' => 'libelle',
                        'multiple' => true //Obligatoire dans ce cas en ManytoMany : sinon il ne renvoit pas un tableau et affichera une erreur !
                    ))
                    ->add('nom', FileType::class, array(
                        'label' => 'Fichier à télécharger'
                    ))//File input dans un formulaire
                    ->add('save', SubmitType::class, array(
                        'label' => 'Ajouter'
                    ))//Un boutton submit.
                    ->getForm();
        } else {//Si 'lutilisateur n'est pas ADMIN: la seule différence est qu'i ne pourra pas choisir l'utilisateur attribué au fichier.
            $form = $this->createFormBuilder($fichier)
                    ->add('themes', EntityType::class, array(
                        'class' => 'App\Entity\Theme',
                        'choice_label' => 'libelle',
                        'multiple' => true,
                        'query_builder' => function(ThemeRepository $repository) use($filiere){
                            return $repository->getThemesByFiliere($filiere);
                        }
                    ))
                    ->add('nom', FileType::class, array(
                        'label' => 'Fichier à télécharger'
                    ))
                    ->add('save', SubmitType::class, array(
                        'label' => 'Ajouter'
                    ))
                    ->getForm();
        }
        $form->handleRequest($request); //On traite les données du formulaire
        if ($form->isSubmitted() && $form->isValid()) {//Si tout est bon
            // La variable $file sera de type UploadedFile $file
            $file = $fichier->getNom();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension(); // On renomme le fichier et on lui redonne son extension pour stocker le tout dans $fileName
            if (!$hasAccess) {//Si ce n'est pas un administrateur
                $user = $this->getUser(); //On récupère dans User les infos de l'utilisateur connecté.
                $fichier->setUser($user); //On attribue au fichier les infos de l'utilisateur qui se trouve dans $user.
            }
            $fichier->setNom($fileName); //On set le nom du fichier par le contenu de $filename
            $fichier->setDate(new \DateTime()); //récupère la date du jour
            $fichier->setExtension($file->guessExtension()); // Récupère l’extension du fichier
            $fichier->setTaille($file->getSize()); // getSize contient la taille du fichier envoyé
            $fichier->setNomOriginal($file->getClientOriginalName()); //On set le NomOriginal en récupérant le nom original du fichier.
            $em = $this->getDoctrine()->getManager();
            $em->persist($fichier);
            $em->flush(); //On met à jour la BD
            try {
                $file->move($this->getParameter('file_directory'), $fileName); // Nous déplaçons le fichier dans le répertoire configuré dans services.yaml
            } catch (FileException $e) {
                // erreur durant l’upload
            }
        }


        return $this->render('fichier/ajout.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route({
        "fr" : "/fichier_liste",
     *  "en" : "/file_list",
     *  "de" : "/datei_liste",
     *  "es" : "/archivo_lista"}, name="fichier_liste")
     */
    public function liste(Request $request) {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //Renvoie true si l'utilisateur connecté possède le rôle ADMIN
        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        //On récupère les informations de la table Fichier
        $fichier = new Fichier();
        //On instancie un nouveau Fichier

        $form = $this->createFormBuilder($fichier)
            //On créé le formulaire
                ->add('save', SubmitType::class, array(
                    'attr' => array(
                        'class' => 'save'),
                    'label' => 'Supprimer'))
            //Bouton qui permet de supprimer dans la suite du code
                ->getForm(); //Finition       
        if ($request->isMethod('POST')) {
            //On récupère les informations du formulaire quand il est envoyé
            $form->handleRequest($request);
            //On traite le formulaire
            if ($form->isValid()) {
                //On vérifie qu'il est bien valide
                $cocher = $request->request->get('cocher');
                //On récupère toutes les cases cochées

                foreach ($cocher as $i) {
                    //Pour chaque case cochée
                    $u = $repository->find($i);
                    //On récupère les informations liées à la case cochée
                    $this->getDoctrine()->getManager()->remove($u);
                    //On supprime ces dernières
                }
                $this->getDoctrine()->getManager()->flush();
                //On met à jour la BD
            }
        }
        if ($hasAccess) {
            //S'il l'utilisateur est bien ADMIN
            $listeFichiers = $repository->findAll();
            //On récupère la liste de tous les fichiers

            return $this->render('fichier/liste.html.twig', [
                        'listeFichiers' => $listeFichiers,
                        'form' => $form->createView(),
            ]);
            //Affiche la page twig lié à ce controller et on transmet le formulaire
        }else{
            return $this->redirectToRoute('fichier_maliste');
        }
    }

    /**
     * @Route({
        "fr" : "/fichier_maliste",
     *  "en" : "/file_mylist",
     *  "de" : "/datei_meineliste",
     *  "es" : "/archivo_milista"}, name="fichier_maliste")
     */
    public function maliste(Request $request) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class); //On récupère les informations de la table Fichier
        $fichier = new Fichier();

        $form = $this->createFormBuilder($fichier)//On créé le formulaire
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


        $fichiers = $repository->findBy(['user' => $this->getUser()]); //On récupère les fichiers liés à l'utilisateur en cours



        ;
        return $this->render('fichier/maliste.html.twig', [
                    'listeFichiers' => $fichiers, 'form' => $form->createView(),
        ]); //Affiche la page twig lié à ce controller et on transmet le formulaire
    }

    /**
     * @Route({
        "fr" : "/fichier_modifier/{id}",
     *  "en" : "/file_edit/{id}",
     *  "de" : "/datei_bearbeiten/{id}",
     *  "es" : "/archivo_editar/{id}"}, name="fichier_modifier")
     */
    public function modifier(Request $request) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $hasAccess = $this->isGranted("ROLE_ADMIN");
        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        $fichier = $repository->find($request->get('id'));

        if ($hasAccess) {
            $form = $this->createFormBuilder($fichier)
                    ->add('nom', TextType::class)
                    ->add('nomoriginal', TextType::class)
                    ->add('themes', EntityType::class, array(
                        'class' => 'App\Entity\Theme',
                        'choice_label' => 'libelle',
                        'multiple' => true //Obligatoire dans ce cas en ManytoMany : sinon il ne renvoit pas un tableau et affichera une erreur !
                    ))
                    ->add('date', DateType::class, array(
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                    ))
                    ->add('extension', TextType::class)
                    ->add('taille', IntegerType::class)
                    ->add('user', EntityType::class, array(
                        'class' => 'App\Entity\User',
                        'choice_label' => 'nom'
                    ))
                    ->add('save', SubmitType::class, array('label' => 'Modifier'))
                    ->getForm();
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($fichier);
                    $em->flush();
                }
            }
            return $this->render('fichier/modifier.html.twig', ['form' => $form->createView()]);
        } else if ($fichier->getUser()->getId() == $this->getUser()->getId()) {//Je vérifie si l'ID de l'utilisateur du fichier correspponds à l'ID de l'utilisateur connecté
            $form = $this->createFormBuilder($fichier)
                    ->add('nom', TextType::class)
                    ->add('nomoriginal', TextType::class)
                    ->add('themes', EntityType::class, array(
                        'class' => 'App\Entity\Theme',
                        'choice_label' => 'libelle',
                        'multiple' => true //Obligatoire dans ce cas en ManytoMany : sinon il ne renvoit pas un tableau et affichera une erreur !
                    ))
                    ->add('date', DateType::class, array(
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                    ))
                    ->add('extension', TextType::class)
                    ->add('taille', IntegerType::class)
                    ->add('save', SubmitType::class, array('label' => 'Modifier'))
                    ->getForm();
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    if (!$hasAccess) {//Si ce n'est pas un administrateur
                        $user = $this->getUser(); //On récupère dans User les infos de l'utilisateur connecté.
                        $fichier->setUser($user); //On attribue au fichier les infos de l'utilisateur qui se trouve dans $user.
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($fichier);
                    $em->flush();
                }
            }
            return $this->render('fichier/modifier.html.twig', ['form' => $form->createView()]);
        } else {

            return $this->maliste($request);
        }
    }

    /**
     * @Route("/wsFichiers", name="wsFichiers")
     */
    public function wsFichiers(Request $request,FichierRepository $repository)
    {
        $fichiers = $repository->findAllFilesByUser($this->getUser());
        for ($i=0;$i<count($fichiers);$i++){
            $fichier_src = $fichiers[$i]['nom'];
            $fichierbinary =fread(
                fopen($this->getParameter('file_directory').'/'.$fichier_src,"r"),
                filesize($this->getParameter('file_directory').'/'.$fichier_src));
            $fichiers[$i]['nom'] = base64_encode($fichierbinary);

        }
        return $this->json($fichiers);
    }



}
