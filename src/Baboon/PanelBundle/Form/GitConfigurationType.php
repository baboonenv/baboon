<?php

namespace Baboon\PanelBundle\Form;

use Baboon\PanelBundle\Entity\GitConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GitConfigurationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deployType', ChoiceType::class, [
                'choices' => [
                    'SSH' => 'ssh',
                    'HTTPS' => 'https',
                ]
            ])
            ->add('repo')
            ->add('email')
            ->add('password', PasswordType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => GitConfiguration::class,
                'attr' => [
                    'class' => 'form-validate',
                ],
            ]
        );
    }
}
