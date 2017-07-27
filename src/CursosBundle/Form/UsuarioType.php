<?php

namespace CursosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('persona', EntityType::class, ["label"=>"Nombre y Apellido", "required"=>"required", "class" => "CursosBundle:Persona", "attr"=>[
                "class"=>"form-control"
            ]])*/
            ->add('apodo', TextType::class, ["label"=>"Apodo", "required"=>"required", "attr"=>[
                "class"=>"form-control"
            ]])
            ->add('correo', EmailType::class, ["label"=>"Dirección de correo", "required"=>"required", "attr"=>[
                "class"=>"form-control"
            ]])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas deben coincidir.',
                'options' => ['attr' => ['class' => 'form-control password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Repita la Contraseña'],
            ])
            ->add('enviar', SubmitType::class, ["label"=>"Registrar Usuario", "attr"=>[
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
