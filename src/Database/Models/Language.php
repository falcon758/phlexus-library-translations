<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database\Models;

use Phlexus\Models\Model;

/**
 * Class Language
 *
 * @package Phlexus\Libraries\Translations\Database\Models
 */
class Language extends Model
{
    public const DISABLED = 0;

    public const ENABLED = 1;

    /**
     * @var int|null
     */
    public ?int $id;

    /**
     * @var string
     */
    public string $iso;

    /**
     * @var string
     */
    public string $language;

    /**
     * @var int|null
     */
    public ?int $active;

    /**
     * @var string|null
     */
    public ?string $createdAt;

    /**
     * @var string|null
     */
    public ?string $modifiedAt;

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('language');
    }
}
