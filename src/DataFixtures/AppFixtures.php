<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Fichier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture {

    public function load(ObjectManager $manager) {

        $faker = Faker\Factory::create('fr_FR');
        $roles = array();   
        $roles = array("ROLE_USER", "ROLE_ADMIN");

        for ($i = 0; $i < 50; $i++) {         
            
            $randomRole = array_rand($roles,1);
            unset($lesRoles);
            
            
            $lesRoles[] = $roles[$randomRole];
            
            $utilisateur = new User();
            $utilisateur->setUsername($faker->userName);
            $utilisateur->setRoles($lesRoles);
            $utilisateur->setPassword($faker->password);
            $utilisateur->setNom($faker->firstName);
            $utilisateur->setPrenom($faker->lastName);
            $utilisateur->setDatenaissance($faker->dateTimeThisCentury($max = '2000', $timezone = "Europe/Paris"));
            $utilisateur->setDateinscription(new \DateTime());
            $manager->persist($utilisateur);
            $this->user[] = $utilisateur;
        }


        $themes = array();
        $themes = array('Anglais', 'Communication', 'Droit', 'Algorithmique et Programmation', 'Langages du Web', 'Bases de données', 'Réseaux',
            'Gestionnaire de version', 'Qualité logiciel', 'Génie logiciel', 'Framework PHP', 'Economie et Gestion', 'Management de projet',
            'Plate-formes d\'intégration continue', 'Sécurité', 'Framework Javascript');

        foreach ($themes as $key => $value) {

            $theme = new Theme();
            $theme->setLibelle($value);
            $manager->persist($theme);

            $this->theme[] = $theme;
        }

        for ($i = 0; $i < 50; $i++) {

            $fichier = new Fichier();
            $fichier->setNom($faker->word);
            $fichier->setNomOriginal($faker->mimeType);
            $fichier->setDate($faker->dateTimeThisYear($max = 'now', $timezone = "Europe/Paris"));
            $fichier->setExtension($faker->fileExtension);
            $fichier->setTaille($faker->randomNumber($nbDigits = rand(1, 5), $strict = false));
            $fichier->setUser($this->user[rand(0, count($this->user) - 1)]);
            $randomTheme[] = array_rand($this->theme,rand(1,count($this->theme)));
         
            
            foreach($randomTheme as $key => $value){
                
            if(isset($value[$key])){ // Trick pour contourner l'erreur undefined offset, A voir dans l'avenir...
                $fichier->addTheme($this->theme[$value[$key]]);
         
                 }
            }

            $manager->persist($fichier);
        }



        $manager->flush();
    }

}
