<?php

namespace Baboon\PanelBundle\Params;

use Jb\Bundle\FileUploaderBundle\Form\Type\CropImageAjaxType;
use Jb\Bundle\FileUploaderBundle\Form\Type\FileAjaxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AssetTypes
{
    const TEXT = 'text';
    const STRING = 'string';
    const MARKDOWN = 'markdown';
    const RICHTEXT = 'richtext';
    const IMAGE = 'image';
    const FILE = 'file';
    const DATETIME = 'datetime';
    const CHOICES = 'choices';
    const EMAIL = 'email';
    const COLOR = 'color';
    const TREE = 'tree';

    /**
     * @return array
     */
    static function getAssetTypes()
    {
        return [
            self::TEXT,
            self::STRING,
            self::MARKDOWN,
            self::RICHTEXT,
            self::IMAGE,
            self::FILE,
            self::DATETIME,
            self::CHOICES,
            self::EMAIL,
            self::COLOR,
            self::TREE,
        ];
    }

    /**
     * @return array
     */
    static function getAssetFormTypes()
    {
        return [
            self::TEXT => TextareaType::class,
            self::STRING => TextType::class,
            self::MARKDOWN => TextareaType::class,
            self::RICHTEXT => TextareaType::class,
            self::IMAGE => CropImageAjaxType::class,
            self::FILE => FileAjaxType::class,
            self::DATETIME => DateTimeType::class,
            self::CHOICES => ChoiceType::class,
            self::EMAIL => EmailType::class,
            self::COLOR => TextType::class,
            self::TREE => FormType::class,
        ];
    }

    /**
     * @param $asset
     * @return mixed
     */
    static function getAssetFormType($asset)
    {
        return self::getAssetFormTypes()[$asset];
    }
}
