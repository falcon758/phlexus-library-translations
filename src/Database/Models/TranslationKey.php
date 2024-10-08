<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database\Models;

use Phlexus\Models\Model;

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
     * @var int|null
     */
    public ?int $id = null;

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
    public ?int $active = null;

    /**
     * @var string|null
     */
    public ?string $createdAt = null;

    /**
     * @var string|null
     */
    public ?string $modifiedAt = null;

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
            ->columns([
                $t_model .'.key AS key',
                $t_model .'.textTypeID AS textTypeID',
                $t_model .'.pageID AS pageID',
                'T.translation AS translation'
            ])
            ->innerJoin(Translation::class, null, 'T')
            ->innerJoin(TextType::class, null, 'TT')
            ->innerJoin(Page::class, null, 'PG')
            ->innerJoin(Language::class, 'T.languageID = LNG.id', 'LNG')
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
    public static function createTranslationKey(string $key, string $page, string $type): ?TranslationKey
    {
        $pageModel = Page::findFirstByname($page);
        $typeModel = TextType::findFirstBytype($type);

        if (!$pageModel || !$typeModel) {
            return null;
        }

        $translationKey = self::findFirst([
            'conditions' => 'active = :active: AND key = :key: AND pageID = :pageID: AND textTypeID = :textTypeID:',
            'bind'       => [
                'active'     => self::ENABLED,
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
    ): ?Translation
    {
        $languageModel = Language::findFirstBylanguage($language);

        $translationKey = self::createTranslationKey($key, $page, $type);

        if (!$translationKey || !$languageModel) {
            return null;
        }

        $translationModel = Translation::findFirst([
            'conditions' => 'active = :active: AND translationKeyID = :translationKeyID: AND languageID = :languageID:',
            'bind'       => [
                'active'           => self::ENABLED,
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
