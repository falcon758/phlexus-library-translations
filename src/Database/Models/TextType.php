<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database\Models;

use Phlexus\Models\Model;

/**
 * Class TextType
 *
 * @package Phlexus\Libraries\Translations\Database\Models
 */
class TextType extends Model
{
    public const DISABLED = 0;

    public const ENABLED = 1;

    /**
     * PAGE
     */
    public const PAGE = 'page';

    /**
     * Message
     */
    public const MESSAGE = 'message';

    /**
     * Form
     */
    public const FORM = 'form';

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var string
     */
    public string $name;

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
        $this->setSource('text_type');
    }
}
