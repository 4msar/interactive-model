# WIP: Interactive Model for Laravel

[![Latest Stable Version](https://poser.pugx.org/4msar/interactive-model/v/stable)](https://packagist.org/packages/4msar/interactive-model)  [![Total Downloads](https://poser.pugx.org/4msar/interactive-model/downloads)](https://packagist.org/packages/4msar/interactive-model) [![Latest Unstable Version](https://poser.pugx.org/4msar/interactive-model/v/unstable)](https://packagist.org/packages/4msar/interactive-model) [![License](https://poser.pugx.org/4msar/interactive-model/license)](https://packagist.org/packages/4msar/interactive-model)

## Getting Started

To get started you need to install the package with Composer:

```bash
composer require 4msar/interactive-model
```

### Laravel >= 5.5

That's it! The package is auto-discovered on 5.5 and up!

### Laravel <= 5.4

Add the package service provider to your providers array in `config/app.php`

```php
'providers' => [
    // ...
    MSAR\InteractiveModel\InteractiveModelServiceProvider,
],
```

To start using this package, run this command in your terminal and follow the onscreen prompts:

```bash
php artisan interactive
```


## Disclaimer

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
