<?php

namespace Baboon\PanelBundle\Form;

use Baboon\PanelBundle\Entity\FTPConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FTPConfigurationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hostname', TextType::class)
            ->add('username', TextType::class, [
                'attr' => [
                    'autocomplete' => 'false',
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'false',
                ]
            ])
            ->add('path', TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => FTPConfiguration::class,
                'attr' => [
                    'class' => 'form-validate',
                ],
            ]
        );
    }
}
