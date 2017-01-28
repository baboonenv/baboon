<?php

namespace Baboon\PanelBundle\Form;

use Baboon\PanelBundle\Entity\UploadImage;
use Jb\Bundle\FileUploaderBundle\Form\Type\CropImageAjaxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', CropImageAjaxType::class, [
                'endpoint' => 'gallery',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => UploadImage::class,
                'attr' => [
                    'class' => 'form-validate',
                ],
            )
        );
    }
}
