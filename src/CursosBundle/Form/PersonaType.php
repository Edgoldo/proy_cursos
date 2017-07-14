<?php

namespace CursosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PersonaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, ["label"=>"Nombre", "required"=>"required", "attr"=>[
                "class"=>"form-control"
            ]])
            ->add('apellido', TextType::class, ["label"=>"Apellido", "required"=>"required", "attr"=>[
                "class"=>"form-control"
            ]])
            ->add('direccion', TextareaType::class, ["label"=>"Dirección", "required"=>"required", "attr"=>[
                "class"=>"form-control"
            ]])
            ->add('Registrarse', SubmitType::class, ["attr"=>[
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
            'data_class' => 'CursosBundle\Entity\Persona'
        ));
    }
}
