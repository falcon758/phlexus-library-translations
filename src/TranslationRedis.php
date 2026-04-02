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
use Phlexus\Libraries\Translations\Redis;
use Phlexus\Libraries\Translations\Database\DatabaseAdapter;
use Phlexus\Libraries\Translations\Database\Models\TranslationKey as TranslationModel;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class TranslationRedis extends TranslationAbstract
{
    /**
     * Get translation factory
     * 
     * @param string $page Page to translate
     * @param string $type Type to translate
     * 
     * @return AdapterInterface
     */
    public function getTranslateFactory(string $page, string $type): AdapterInterface
    {
        $redis = new Redis(
            new InterpolatorFactory(),
            [
                'locale' => $this->language,
                'page'   => $page,
                'type'   => $type,
                'redis'  => $this->redis,
                'levels' => 5,
            ]
        );

        // Avoid unecessary quering if already loaded
        $translations = !$redis->hasChache() ? $this->getAll($page, $type) : [];

        foreach ($translations as $key => $translation) {
            // If key already exists, assume it's already loaded
            if ($redis->exists($key)) {
                break;
            }

            $redis->add($key, $translation);
        }

        return $redis;
    }

    /**
     * Get all translations
     * 
     * @param string $page Page to translate
     * @param string $type Type to translate
     * 
     * @return array
     */
    private function getAll(string $page, string $type): array
    {
        $translations = $this->parseTranslations(
            TranslationModel::getTranslationsType($page, $type, $this->language)
        );

        if (isset($this->defaultLanguage) && $this->defaultLanguage !== $this->language) {
            $defaultTranslations = $this->parseTranslations(
                TranslationModel::getTranslationsType($page, $type, $this->defaultLanguage)
            );

            $translations = array_replace($defaultTranslations, $translations);
        }

        return $translations;
    }

    /**
     * Parse translation rows into key-value pairs.
     *
     * @param array $translations
     *
     * @return array
     */
    private function parseTranslations(array $translations): array
    {
        $parsedTranslations = [];

        array_walk($translations, function (&$value, $key) use (&$parsedTranslations) {
            $parsedTranslations[$value['key']] = $value['translation'];
        });

        return $parsedTranslations;
    }
}