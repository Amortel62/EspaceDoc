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
     * @Route("/fichier_ajout", name="fichier_ajout")
     */
    public function ajout(Request $request) {
        $fichier = new Fichier();
        $form = $this->createFormBuilder($fichier)
                ->add('utilisateur', EntityType::class, array(
                    'class' => 'App\Entity\Utilisateur',
                    'choice_label' => 'nom'
                ))
                ->add('nom', FileType::class, array('label' => 'Fichier à télécharger'))
                ->add('save', SubmitType::class, array('label' => 'Ajouter'))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // La variable $file sera de type UploadedFile $file

            $file = $fichier->getNom();
            // On renomme le fichier et on lui redonne son extension pour stocker le tout dans $fileName
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
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

        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        $fichier = new Fichier();
        $form = $this->createFormBuilder($fichier)
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
        $listeFichiers = $repository->findAll();

        return $this->render('fichier/liste.html.twig', [
                    'listeFichiers' => $listeFichiers, 'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fichier_modifier/{id}", name="fichier_modifier")
     */
    public function modifier(Request $request) {
        $repository = $this->getDoctrine()->getManager()->getRepository(Fichier::class);
        $fichier = $repository->find($request->get('id'));
        $form = $this->createFormBuilder($fichier)
                ->add('nom', TextType::class)
                ->add('nomoriginal',TextType::class)
                ->add('date', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ))
                ->add('extension', TextType::class)
                ->add('taille', IntegerType::class)
                ->add('utilisateur', EntityType::class, array(
                    'class' => 'App\Entity\Utilisateur',
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
