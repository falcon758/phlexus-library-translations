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

use Phlexus\Libraries\Translations\Database\Models\Page;
use Phlexus\Libraries\Translations\Database\Models\TextType;
use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\AdapterInterface;

/**
 * Class TranslationAbstract
 */
abstract class TranslationAbstract extends Injectable implements TranslationInterface
{
    /**
     * Language
     */
    protected string $language;

    /**
     * Default Language
     */
    protected string $defaultLanguage;

    /**
     * Page
     */
    protected string $page;

    /**
     * Type
     */
    protected string $type;

    /**
     * AdapterInterface
     */
    private array $translator = [];

    /**
     * Call interceptor
     * 
     * @param string $method Method
     * @param array  $params params
     * 
     * @throws Exception
     */
    public function __call($method, array $params) {
        $handler = [$this->getTranslator(), $method];

        if (is_callable($handler)) {
            return call_user_func_array($handler , $params);
        }

        throw new \Exception('Unable to call translation method');
    }

    /**
     * Construct language
     * 
     * @param string $language        Preferred language
     * @param string $defaultLanguage Fallback language
     */
    public function __construct(string $language, string $defaultLanguage) {
        if (preg_match('/^[a-zA-Z-]+$/', $language) !== 1) {
            throw new \Exception('Unable to setup translation!');
        }

        $this->language        = strtolower($language);
        $this->defaultLanguage = strtolower($defaultLanguage);

        $this->setPage();
        $this->setType();
    }

    /**
     * Set page and type
     * 
     * @param string $page Page
     * @param string $type Type
     * 
     * @return TranslationInterface
     */
    public function setPageType(string $page = '', string $type = ''): TranslationInterface
    {
        $this->setPage($page);
        $this->setType($type);

        return $this;
    }

    /**
     * Set page
     * 
     * @param string $page Page
     * 
     * @return TranslationInterface
     */
    public function setPage(string $page = ''): TranslationInterface
    {
        $this->page = !empty($page) ? strtolower($page) : Page::DEFAULTPAGE;

        return $this;
    }

    /**
     * Set type
     * 
     * @param string $type Type
     * 
     * @return TranslationInterface
     */
    public function setType(string $type = ''): TranslationInterface
    {
        $this->type = !empty($type) ? strtolower($type) : TextType::PAGE;

        return $this;
    }

    /**
     * Set type page
     * 
     * @return TranslationInterface
     */
    public function setTypePage(): TranslationInterface
    {
        $this->type = TextType::PAGE;

        return $this;
    }

    /**
     * Set type message
     * 
     * @return TranslationInterface
     */
    public function setTypeMessage(): TranslationInterface
    {
        $this->type = TextType::MESSAGE;

        return $this;
    }

    /**
     * Set type form
     * 
     * @return TranslationInterface
     */
    public function setTypeForm(): TranslationInterface
    {
        $this->type = TextType::FORM;

        return $this;
    }

    /**
     * Get translations
     * 
     * @return AdapterInterface
     */
    public function getTranslator(): AdapterInterface
    {
        $translateName = $this->page . '_' . $this->type;

        if (count($this->translator) > 0 && isset($this->factory[$translateName])) {
            return $this->translator[$translateName];
        }

        $this->translator[$translateName] = $this->getTranslateFactory($this->page, $this->type);

        return $this->translator[$translateName];
    }
}