<?php
namespace AppBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
class NewPassType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newPassword', RepeatedType::class, [
                'required' => false,
                'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                'mapped' => false,
                'invalid_message' => 'Las contraseñas no coinciden',
                'first_options' => [
                    'label' => false,
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => 'La contraseña debe tener 8 caracteres como mínimo'
                        ]),
                        new NotNull()
                    ],
                    'attr' => [
                        'placeholder' => 'Nueva contraseña',
                        'tabindex' => 2,
                        'autofocus' => '',
                        'style' => 'border-bottom: 1px solid #e0e0e0',
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Repite la contraseña',
                        'tabindex' => 3,
                    ]
                ]
            ]);
    }
}