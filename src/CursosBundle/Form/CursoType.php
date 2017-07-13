<?php

namespace CursosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CursoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', TextType::class, ["label"=>"Titulo", "required"=>"required", "attr"=>[
                "class"=>"form-apodo form-control"
            ]])
            ->add('descripcion', TextareaType::class, ["label"=>"Descripción", "required"=>"required", "attr"=>[
                "class"=>"form-apodo form-control"
            ]])
            ->add('duracion', TextType::class, ["label"=>"Duración en Semanas", "required"=>"required", "attr"=>[
                "class"=>"form-apodo form-control"
            ]])
            ->add('participantes', TextType::class, ["label"=>"Número de Participantes", "required"=>"required", "attr"=>[
                "class"=>"form-apodo form-control"
            ]])
            ->add('Registrar Curso', SubmitType::class, ["attr"=>[
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
            'data_class' => 'CursosBundle\Entity\Curso'
        ));
    }
}
