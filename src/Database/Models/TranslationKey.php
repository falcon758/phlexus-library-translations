<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database\Models;

use Phalcon\Mvc\Model;

/**
 * Class TranslationKey
 *
 * @package Phlexus\Libraries\Translations\Database\Models
 */
class TranslationKey extends Model
{
    public const DISABLED = 0;

    public const ENABLED = 1;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public string $key;

    /**
     * @var int
     */
    public int $textTypeID;

    /**
     * @var int
     */
    public int $pageID;

    /**
     * @var int|null
     */
    public $active;

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('translation_keys');

        $this->hasMany('id', Translation::class, 'translationKeyID', [
            'alias'      => 'translationKey',
            'foreignKey' => [
                'message' => 'TranslationKey is being used on Translation',
            ],
        ]);

        $this->hasOne('textTypeID', TextType::class, 'id', [
            'alias'    => 'TextType',
            'reusable' => true,
        ]);

        $this->hasOne('pageID', Page::class, 'id', [
            'alias'    => 'Page',
            'reusable' => true,
        ]);
    }


    /**
     * Get translation by Page and Type
     * 
     * @param string $page     Page to translate
     * @param string $type     Type to translate
     * @param string $Language Language to use
     * 
     * @return array
     */
    public static function getTranslationsType(string $page, string $type, string $language): array
    {
        $t_model = self::class;

        return self::query()
            ->columns(
                $t_model .'.key AS key,'
                . $t_model .'.textTypeID AS textTypeID,'
                . $t_model .'.pageID AS pageID,'
                . 'T.translation AS translation'
            )
            ->innerJoin(Translation::class, null, 'T')
            ->innerJoin(TextType::class, null, 'TT')
            ->innerJoin(Page::class, null, 'PG')
            ->innerJoin(Language::class, null, 'LNG')
            ->where('PG.name = :pageName: AND TT.type = :textType: AND LNG.language = :language:', [
                'pageName' => $page,
                'textType' => $type,
                'language' => $language
            ])
            ->execute()
            ->toArray();
    }

    /**
     * Create translation key
     *
     * @param string $key         Key
     * @param string $page        Page
     * @param string $type        Type
     * 
     * @return mixed TranslationKey or null
     */
    public static function createTranslationKey(string $key, string $page, string $type)
    {
        $pageModel = Page::findFirstByname($page);
        $typeModel = TextType::findFirstBytype($type);

        if (!$pageModel || !$typeModel) {
            return null;
        }

        $translationKey = self::findFirst([
            'conditions' => 'key = :key: AND pageID = :pageID: AND textTypeID = :textTypeID:',
            'bind'       => [
                'key'        => $key,
                'pageID'     => $pageModel->id,
                'textTypeID' => $typeModel->id
            ],
        ]);

        if ($translationKey) {
            return $translationKey;
        }

        $translationKeyModel              = new self;
        $translationKeyModel->key         = $key;
        $translationKeyModel->textTypeID  = (int) $typeModel->id;
        $translationKeyModel->pageID      = (int) $pageModel->id;

        return $translationKeyModel->save() ? $translationKeyModel : null;
    }

    /**
     * Create translation with Page and Type
     * 
     * @param string $key         Key
     * @param string $page        Page
     * @param string $type        Type
     * @param string $Language    Language
     * @param string $translation Translation
     * 
     * @return mixed Translation or null
     */
    public static function createTranslationsType(
        string $key, string $page, string $type,
        string $language, string $translation
    )
    {
        $languageModel = Language::findFirstBylanguage($language);

        $translationKey = self::createTranslationKey($key, $page, $type);

        if (!$translationKey || !$languageModel) {
            return false;
        }

        $translationModel = Translation::findFirst([
            'conditions' => "translationKeyID = :translationKeyID: AND languageID = :languageID:",
            'bind'       => [
                'translationKeyID' => $translationKey->id,
                'languageID'       => $languageModel->id
            ],
        ]);

        if ($translationModel) {
            return $translationModel;
        }

        $translationModel                   = new Translation;
        $translationModel->translation      = $translation;
        $translationModel->translationKeyID = (int) $translationKey->id;
        $translationModel->languageID       = (int) $languageModel->id;

        return $translationModel->save() ? $translationModel : null;
    }
}
