<?php

namespace Baboon\PanelBundle\Form;

use Baboon\PanelBundle\Entity\UploadFile;
use Jb\Bundle\FileUploaderBundle\Form\Type\FileAjaxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileAjaxType::class, $options['file_options'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => UploadFile::class,
                'file_options' => [],
                'attr' => [
                    'class' => 'form-validate',
                ],
            )
        );
    }
}
