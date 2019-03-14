<?php

namespace App\Form;

use App\Entity\Departement;
use Doctrine\ORM\EntityManagerInterface;
use function Sodium\add;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccueilType extends AbstractType{

   /* private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }*/

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('departement', EntityType::class, array(
                'class' => 'App\Entity\Departement',
                'choice_label' => 'nom',
                'mapped' => false,
                'required' => false,
                'placeholder' => 'En cours de développement',
                'disabled' => true
            ))
            ->add('filiere', EntityType::class,array(
                'class' => 'App\Entity\Filiere',
                'choice_label' => 'nom',
                'required' => true,
                'placeholder' => 'Sélectionnez votre filière'
            ));

            $builder->add('save', SubmitType::class, array('label' => 'S\'inscrire'));
/*
            $builder->get('departement')->addEventListener(
                FormEvents::PRE_SET_DATA,
                function(FormEvent $event){
                   $data = $event->getData();
                   if(!$data){
                       return;
                   }
                   $this->setupFiliereNameField(
                       $event->getForm(),
                       $data->getDepartement()

                   );
                }
            );

    }
    private function setupFiliereNameField(FormInterface $form, ?Departement $departement)
    {
        if (null === $departement) {
            $form->remove('filiere');
            return;
        }

        $choices = $departement->getFilieres();

        if (null === $choices){
            $form->remove('filiere');
            return;
        }
        $form->add('filiere',ChoiceType::class,[
            'placeholder' => 'Sélectionnez votre filière',
            'choices' => $choices
        ]);
    }
*/
    }
}


