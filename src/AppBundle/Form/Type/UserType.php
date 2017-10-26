<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nombre',
                ],
                'required' => true
            ])
            ->add('apellidos', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Apellidos',
                ],
                'required' => true
            ])
            ->add('fechaNacimiento', null, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-fecha',
                    'placeholder'=>'aaaa-mm-dd'
                ],
                'format' => 'Y-M-d',
            ])
            ->add('ciudad', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Ciudad',
                ],
                'required' => false
            ])
            ->add('provincia', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Provincia',
                ],
                'required' => false
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Sobre mí',
                ],
                'required' => false
            ])
            ->add('imagenPerfil', FileType::class, [
                'label' => false,
                'required' => false,
                'data_class' => null,

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