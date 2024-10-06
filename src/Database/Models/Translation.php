<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database\Models;

use Phlexus\Models\Model;

/**
 * Class Translation
 *
 * @package Phlexus\Libraries\Translations\Database\Models
 */
class Translation extends Model
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
    public string $translation;

    /**
     * @var int
     */
    public int $translationKeyID;

    /**
     * @var int
     */
    public int $languageID;

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
        $this->setSource('translations');

        $this->hasOne('translationKeyID', TranslationKey::class, 'id', [
            'alias'    => 'translationKey',
            'reusable' => true,
        ]);

        $this->hasOne('languageID', Language::class, 'id', [
            'alias'    => 'language',
            'reusable' => true,
        ]);
    }
}
