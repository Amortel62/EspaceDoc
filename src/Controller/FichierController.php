<?php

namespace App\Controller;

use App\Entity\Fichier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FichierController extends AbstractController {

    /**
     * @return string
     */
    private function generateUniqueFileName() {
        return md5(uniqid());
    }

    /**
     * @Route("/download/{id}", name="download")
     */
    public function download(Request $request) {

        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        $fichier = $repository->find($request->get('id')); // On récupère le fichier grâce à l'id passé dans l'URL
        $idfichiers = $repository->findBy(['id' => $this->getUser()->getId()]); // On récupère les ID des fichiers appartenant à l'utilisateur connecté
        $hasAccess = $this->isGranted('ROLE_ADMIN'); //J'instancie une variable pour vérifier plus tard si le rôle de l'utilisateur est bien admin

        foreach ($idfichiers as $key => $value) {
            if ($fichier->getUser()->getId() == $value->getId() || $hasAccess) {

                return $this->file($this->getParameter('file_directory') . '/' . $fichier->getNom());
                
            } else {

             return $this ->maliste($request);
                
            }
        }
    }

    /**
     * @Route("/fichier_ajout", name="fichier_ajout")
     */
    public function ajout(Request $request) {
        $hasAccess = $this->isGranted('ROLE_ADMIN');

        $fichier = new Fichier();
        if ($hasAccess == true) {
            $form = $this->createFormBuilder($fichier)
                    ->add('user', EntityType::class, array(
                        'class' => 'App\Entity\User',
                        'choice_label' => 'nom'
                    ))
                    ->add('themes', EntityType::class, array(
                        'class' => 'App\Entity\Theme',
                        'choice_label' => 'libelle',
                        'multiple' => true //Obligatoire dans ce cas en ManytoMany : sinon il ne renvoit pas un tableau et affichera une erreur !
                    ))
                    ->add('nom', FileType::class, array('label' => 'Fichier à télécharger'))
                    ->add('save', SubmitType::class, array('label' => 'Ajouter'))
                    ->getForm();
        } else {
            $form = $this->createFormBuilder($fichier)
                    ->add('themes', EntityType::class, array(
                        'class' => 'App\Entity\Theme',
                        'choice_label' => 'libelle',
                        'multiple' => true //Obligatoire dans ce cas en ManytoMany : sinon il ne renvoit pas un tableau et affichera une erreur !
                    ))
                    ->add('nom', FileType::class, array('label' => 'Fichier à télécharger'))
                    ->add('save', SubmitType::class, array('label' => 'Ajouter'))
                    ->getForm();
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // La variable $file sera de type UploadedFile $file

            $file = $fichier->getNom();
            // On renomme le fichier et on lui redonne son extension pour stocker le tout dans $fileName
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
            if ($hasAccess !== true) {
                $user = $this->getUser();
                $fichier->setUser($user);
            }
            $fichier->setNom($fileName);
            $fichier->setDate(new \DateTime()); //récupère la date du jour
            $fichier->setExtension($file->guessExtension()); // Récupère l’extension du fichier
            $fichier->setTaille($file->getSize()); // getSize contient la taille du fichier envoyé
            $fichier->setNomOriginal($file->getClientOriginalName());
            $em = $this->getDoctrine()->getManager();
            $em->persist($fichier);
            $em->flush();
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
     * @Route("/fichier_liste", name="fichier_liste")
     */
    public function liste(Request $request) {

        $hasAccess = $this->isGranted('ROLE_ADMIN'); //Renvoie true si l'utilisateur connecté possède le rôle ADMIN

        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class); //On récupère les informations de la table Fichier
        $fichier = new Fichier(); //On instancie une nouvelle instance de Fichier

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
        if ($hasAccess == true) {//S'il l'utilisateur est bien ADMIN
            $listeFichiers = $repository->findAll(); //On récupère la liste de tous les fichiers
            return $this->render('fichier/liste.html.twig', [
                        'listeFichiers' => $listeFichiers, 'form' => $form->createView(),
            ]); //Affiche la page twig lié à ce controller et on transmet le formulaire
        }
    }

    /**
     * @Route("/fichier_maliste", name="fichier_maliste")
     */
    public function maliste(Request $request) {


        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class); //On récupère les informations de la table Fichier
        $fichier = new Fichier(); //On instancie une nouvelle instance de Fichier

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
        return $this->render('fichier/maliste.html.twig', [
                    'listeFichiers' => $fichiers, 'form' => $form->createView(),
        ]); //Affiche la page twig lié à ce controller et on transmet le formulaire
    }

    /**
     * @Route("/fichier_modifier/{id}", name="fichier_modifier")
     */
    public function modifier(Request $request) {
        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        $fichier = $repository->find($request->get('id'));
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
    }

}
