<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database\Models;

use Phalcon\Mvc\Model;

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
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $iso;

    /**
     * @var string
     */
    public string $language;

    /**
     * @var int
     */
    public int $active;

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
