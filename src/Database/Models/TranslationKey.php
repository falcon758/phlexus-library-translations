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
            ->columns($t_model .'.*, T.translation')
            ->innerJoin(Translation::class, null, 'T')
            ->innerJoin(TextType::class, null, 'TT')
            ->innerJoin(Page::class, null, 'PG')
            ->innerJoin(Language::class, null, 'LNG')
            ->where('TT.type = :textType: AND PG.name = :pageName: AND LNG.language = :language:', [
                'textType' => $type,
                'pageName' => $page,
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

        $translationKey = self::findFirstByKey($key);

        if ($translationKey) {
            return $translationKey;
        }

        $translationModel              = new self;
        $translationModel->key         = $key;
        $translationModel->textTypeID  = $typeModel->id;
        $translationModel->pageID      = $pageModel->id;

        return $translationModel->save() ? $translationModel : null;
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

        $translationModel                 = new Translation;
        $translationModel->translation    = $translation;
        $translationModel->translationKey = $translationKey->id;
        $translationModel->languageID     = $languageModel->id;

        return $translationModel->save() ? $translationModel : null;
    }
}
