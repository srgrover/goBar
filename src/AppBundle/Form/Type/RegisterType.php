<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, [
                'label' => 'Nombre',
                'required' => true
            ])
            ->add('apellidos', null, [
                'label' => 'Apellidos',
                'required' => true
            ])
            ->add('email', null, [
                'label' => 'NIE',
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'required' => true
            ])
            ->add('fechaNacimiento', null, [
                'label' => 'Nacimiento',
                'required' => true
            ])
            ->add('ciudad', null, [
                'label' => 'Ciudad',
                'required' => true
            ])
            ->add('provincia', null, [
                'label' => 'Provincia',
                'required' => true
            ])
            ->add('pais', null, [
                'label' => 'País',
                'required' => true
            ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_usuario';
    }
}