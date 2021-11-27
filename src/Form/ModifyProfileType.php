<?php

namespace App\Form;
 
use App\Entity\User; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;  
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
;

class ModifyProfileType extends AbstractType  
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           
           
            ->add('tel', TextType::class, array('attr' => array('class' => 'form-control'), ))
            ->add('adress', TextType::class, array('attr' => array('class' => 'form-control'), ))
            ->add('submit', SubmitType::class, array('attr' => array( 'class' => 'btn btn-success btn-block')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
