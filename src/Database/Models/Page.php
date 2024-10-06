<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database\Models;

use Phlexus\Models\Model;

/**
 * Class Page
 *
 * @package Phlexus\Libraries\Translations\Database\Models
 */
class Page extends Model
{
    public const DISABLED = 0;

    public const ENABLED = 1;

    /**
     * DefaultPage
     */
    public const DEFAULTPAGE = 'default';

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
        $this->setSource('pages');
    }

    /**
     * Create page
     * 
     * @param string $name Page name
     * 
     * @return mixed Page or null
     */
    public static function createPage(string $name): ?Page
    {
        $page = self::findFirstByname($name);

        if ($page) {
            return $page;
        }

        $page       = new self;
        $page->name = $name;

        return $page->save() ? $page : null;
    }
}
