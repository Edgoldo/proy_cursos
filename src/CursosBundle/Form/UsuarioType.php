<?php

namespace CursosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('apodo', TextType::class, ["label"=>"Apodo", "required"=>"required", "attr"=>[
                "class"=>"form-apodo form-control"
            ]])
            ->add('correo', EmailType::class, ["label"=>"Dirección de correo", "required"=>"required", "attr"=>[
                "class"=>"form-apodo form-control"
            ]])
            ->add('password', PasswordType::class, ["label"=>"Contraseña", "required"=>"required", "attr"=>[
                "class"=>"form-apodo form-control"
            ]])
            ->add('Registrar Usuario', SubmitType::class, ["attr"=>[
                "class"=>"form-submit btn btn-success"
            ]])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CursosBundle\Entity\Usuario'
        ));
    }
}
