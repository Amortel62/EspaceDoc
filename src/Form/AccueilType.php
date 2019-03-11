<?php

namespace App\Form;

use App\Entity\Departement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AccueilType extends AbstractType{


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
                'placeholder' => 'WORK IN PROGRESS',
            ))
            ->add('save', SubmitType::class, array('label' => 'S\'inscrire'));
        $builder->get('departement')->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event){
                $form = $event->getForm();
                $this->addFiliereField($form->getParent(),$form->getData());
            }
        );
    }
    public function addFiliereField(FormInterface $form, ?Departement $departement){
        $form->add('filiere',EntityType::class,[
            'class' =>'App\Entity\Filiere',
            'placeholder' => $departement ? 'Sélectionnez votre filière' : 'Sélectionnez votre département',
            'choices' => $departement ? $departement->getFilieres() : []
        ]);
    }


}