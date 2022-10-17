<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Translations\Database;

use Phlexus\Models\Model;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\Adapter\AbstractAdapter;
use Phalcon\Translate\InterpolatorFactory;

class DatabaseAdapter extends AbstractAdapter implements AdapterInterface
{
   /**
    * Options
    *
    * @var array
    */
   protected array $options;

   /**
    * Translations
    *
    * @var array
    */
    private array $translations;

    /**
     * Model object.
     *
     * @var \Phlexus\Models\Model
     */
    protected Model $model;

    /**
     * Locale.
     *
     * @var string
     */
    protected string $locale;

    /**
     * Default defaultLocale
     *
     * @var string
     */
    protected string $defaultLocale;

    /**
     * Page
     *
     * @var string
     */
    protected string $page;

    /**
     * Type
     *
     * @var string
     */
    protected string $type;

    /**
     * Constructor
     * 
     * @param InterpolatorFactory $interpolator
     * @param array $options
     */
    public function __construct(InterpolatorFactory $interpolator, array $options = [])
    {
        if (!isset($options['model'])) {
            throw new \Exception("Parameter 'model' is required");
        } else if (!$options['model'] instanceof Model) {
            throw new \Exception("Parameter 'model' must be a Model object");
        }

        $this->model = $options['model'];

        if (!isset($options['locale'])) {
            throw new \Exception("Parameter 'locale' is required");
        }

        $this->locale = $options['locale'];

        if (isset($options['defaultLocale'])) {
            $this->defaultLocale = $options['defaultLocale'];
        }

        $this->options = $options;

        if (isset($options['page']) && isset($options['type'])) {
            $this->page = $options['page'];
            $this->type = $options['type'];

            $this->loadAll();
        }

        parent::__construct($interpolator, $options);
    }

    /**
     * Query index for translation
     * 
     * @param string $index
     * @param array  $placeholders
     * 
     * @return string
     */
    public function query(string $index, array $placeholders = []): string
    {
        $value = $this->exists($index) ? $this->translations[$index] : $index;

        return $this->replacePlaceholders($value, $placeholders);   
    }

    /**
     * Check if index exists
     * 
     * @param  string $index
     * 
     * @return bool
     */
    public function exists(string $index): bool
    {
        $exists = isset($this->translations[$index]);

        if (!$exists) {
            $this->getModel()->createTranslationsType($index, $this->page, $this->type, $this->locale, $index);
        }

        return $exists ? true : false;
    }

    /**
     * Check if has index
     * 
     * @param  string $index
     * 
     * @return bool
     */
    public function has(string $index): bool
    {
        return $this->exists();
    }

     /**
     * Adds a translation for given key
     *
     * @param  string  $index
     * @param  string  $message
     *
     * @return boolean
     */
    public function add(string $index, string $message): bool
    {
        if ($this->exists($index)) {
            return false;
        }
        
        if (!$this->model->createTranslationsType(
            $index, $this->page, $this->type,
            $this->language, $message
        )) {
            return false;
        }

        $this->translations[$index] = $message;

        return true;
    }

    /**
     * Update a translation for given key
     *
     * @param  string  $index
     * @param  string  $message
     *
     * @return boolean
     */
    public function update(string $index, string $message): bool
    {
        return $this->add($index, $message);
    }

    /**
     * Deletes a translation for given key
     *
     * @param  string  $index
     *
     * @return boolean
     */
    public function delete(string $index): bool
    {
        if (!$this->exists($index)) {
            return false;
        }

        unset($this->translations[$index]);

        return true;
    }

    /**
     * Sets (insert or updates) a translation for given key
     *
     * @param  string  $index
     * @param  string  $message
     *
     * @return boolean
     */
    public function set(string $index, string $message): bool
    {
        return $this->exists($index) ?
            $this->update($index, $message) : $this->add($index, $message);
    }

    /**
     * @return Model
     */
    private function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Load all translations
     * 
     * @return void
     */
    private function loadAll(): void
    {
        $model = $this->getModel();

        $translations = $model::getTranslationsType($this->page, $this->type, $this->locale);

        // Fallback to default language
        if (count($translations) === 0 && isset($this->defaultLocale)) {
            $this->locale = $this->defaultLocale;
            $translations = $model::getTranslationsType($this->page, $this->type, $this->locale);
        }

        $parsedTranslations = [];
        
        array_walk($translations, function (&$value,$key) use (&$parsedTranslations) {
            $parsedTranslations[$value['key']] = $value['translation'];
        });

        $this->translations = $parsedTranslations;
    }
}