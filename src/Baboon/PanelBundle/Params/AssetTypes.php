<?php

namespace Baboon\PanelBundle\Params;

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
}
