<?php

/**
 * This file is part of the Phlexus CMS.
 *
 * (c) Phlexus CMS <cms@phlexus.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phlexus\Libraries\Translations;

use Phlexus\Helpers;
use Phlexus\Libraries\Translations\Database\DatabaseAdapter;
use Phlexus\Libraries\Translations\Database\Models\TranslationKey;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class TranslationDatabase extends TranslationAbstract
{
    /**
     * Get translation factory
     * 
     * @param string $page Page to translate
     * @param string $type Type to translate
     * 
     * @return AdapterInterface
     */
    public function getTranslateFactory(string $page, string $type): AdapterInterface {
        return new DatabaseAdapter(
            new InterpolatorFactory(),
            [
                'locale'        => $this->language,
                'defaultLocale' => $this->defaultLanguage,
                'model'         => new TranslationKey,
                'page'          => $page,
                'type'          => $type,
            ]
        );
    }
}