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
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var int|null
     */
    public $active;

    /**
     * @var string|null
     */
    public $createdAt;

    /**
     * @var string|null
     */
    public $modifiedAt;

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
