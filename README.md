# Phlexus Translations Library

Translations library for Phlexus CMS.

## Example of usage

```php
use Phlexus\Libraries\Translations\TranslationFactory;

(new TranslationFactory())->build('en-US')
                          ->setPageType('general', TranslationAbstract::PAGE)
``` 

