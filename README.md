# Phlexus Translations Library

Translations library for Phlexus CMS.

## Example of usage

```php
use Phlexus\Libraries\Translations\TranslationFactory;
use Phlexus\Libraries\Translations\Database\Models\Page;
use Phlexus\Libraries\Translations\Database\Models\TextType;

(new TranslationFactory())->build('en-US')
                          ->setPageType(PAGE::DEFAULTPAGE, TextType::PAGE)
``` 

