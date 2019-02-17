<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\Theme;
use App\Entity\Fichier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        $faker = Faker\Factory::create('fr_FR');
        
        for($i = 0;$i < 50;$i++){
            
            $utilisateur = new Utilisateur();
            $utilisateur ->setNom($faker->firstName);
            $utilisateur ->setPrenom($faker->lastName);
            $utilisateur ->setDatenaissance($faker->dateTimeThisCentury($max='2000', $timezone = "Europe/Paris"));
            $utilisateur ->setDateinscription($faker->dateTimeThisMonth($max='now', $timezone = "Europe/Paris"));
                
            $manager->persist($utilisateur);
            
           $this -> utilisateur[] = $utilisateur;
            
            
        }
      
         for($i = 0;$i < 25;$i++){
            
            $theme = new Theme();
            $theme ->setLibelle('Theme '.$i);
  
            $manager->persist($theme);
                     
            
        }
         for($i = 0;$i < 50;$i++){
            
            $fichier = new Fichier();
            $fichier ->setNom($faker->word);
            $fichier ->setDate($faker->dateTimeThisYear($max='now', $timezone = "Europe/Paris"));
            $fichier ->setExtension($faker->fileExtension);
            $fichier ->setTaille($faker->randomNumber($nbDigits = rand(1,5), $strict = false));
            $fichier ->setUtilisateur($this -> utilisateur[rand(0, count($this->utilisateur))]) ;
                
            $manager->persist($fichier);
                     
            
        }
        

        $manager->flush();
    }
}
