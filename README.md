
# 🦅 Loadable objects for Silverstripe

[![Silverstripe Version](https://img.shields.io/badge/Silverstripe-5.1-005ae1.svg?labelColor=white&logoColor=ffffff&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDEuMDkxIDU4LjU1NSIgZmlsbD0iIzAwNWFlMSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBkPSJNNTAuMDE1IDUuODU4bC0yMS4yODMgMTQuOWE2LjUgNi41IDAgMCAwIDcuNDQ4IDEwLjY1NGwyMS4yODMtMTQuOWM4LjgxMy02LjE3IDIwLjk2LTQuMDI4IDI3LjEzIDQuNzg2czQuMDI4IDIwLjk2LTQuNzg1IDI3LjEzbC02LjY5MSA0LjY3NmM1LjU0MiA5LjQxOCAxOC4wNzggNS40NTUgMjMuNzczLTQuNjU0QTMyLjQ3IDMyLjQ3IDAgMCAwIDUwLjAxNSA1Ljg2MnptMS4wNTggNDYuODI3bDIxLjI4NC0xNC45YTYuNSA2LjUgMCAxIDAtNy40NDktMTAuNjUzTDQzLjYyMyA0Mi4wMjhjLTguODEzIDYuMTctMjAuOTU5IDQuMDI5LTI3LjEyOS00Ljc4NHMtNC4wMjktMjAuOTU5IDQuNzg0LTI3LjEyOWw2LjY5MS00LjY3NkMyMi40My0zLjk3NiA5Ljg5NC0uMDEzIDQuMTk4IDEwLjA5NmEzMi40NyAzMi40NyAwIDAgMCA0Ni44NzUgNDIuNTkyeiIvPjwvc3ZnPg==)](https://packagist.org/packages/spatie/schema-org)
[![Package Version](https://img.shields.io/packagist/v/goldfinch/loadable.svg?labelColor=333&color=F8C630&label=Version)](https://packagist.org/packages/spatie/schema-org)
[![Total Downloads](https://img.shields.io/packagist/dt/goldfinch/loadable.svg?labelColor=333&color=F8C630&label=Downloads)](https://packagist.org/packages/spatie/schema-org)
[![License](https://img.shields.io/packagist/l/goldfinch/loadable.svg?labelColor=333&color=F8C630&label=License)](https://packagist.org/packages/spatie/schema-org) 

Load more implementation ⏳ for Silverstripe with front-end component 🌀

## Install

#### 1. Install module
```
composer require goldfinch/loadable
```

#### 2. Add key to your `.env`. To generate the key use [Taz](https://github.com/goldfinch/taz) with the following command `php taz generate:crypto-key` or run php line instead `php bin2hex(random_bytes(16))`

```bash
APP_KEY=""
```

#### 3. Copy config file and amend it as you need
```bash
cp vendor/goldfinch/loadable/_config/loadable.yml.example app/_config/loadable.yml
```

#### 4. Prepare your loadable model

*via extension (recommended)*

```yml
App\Models\MyLoadableModel:
  extensions:
    - Goldfinch\Loadable\Extensions\LoadableExtension
```

*manually*

```php
    public static function loadable($params, $request, $config)
    {
        return MyLoadableModel::get(); // ->limit($params['limit'], $params['start']);
    }

    public function loadableTemplate()
    {
        return $this->renderWith('Loadable/MyLoadableModel');
    }
```

#### 5. Make sure these meta tags are presented in your header

```html
<meta name="csrf-param" content="authenticity_token">
<meta name="csrf-token" content="{$SecurityID}">
```

#### 6. Implemenet JavaScript component that will handle loadmore features

*via Silverstripe Requirements PHP*

```php
Requirements::javascript('goldfinch/loadable:client/dist/loadable.js');
```

*via Silverstripe Requirements template*

```html
<% require javascript('goldfinch/loadable:client/dist/loadable.js') %>
```

*via ES6 module*

```js
import Loadable from '..../vendor/goldfinch/loadable/client/src/src/loadable-mod';

document.addEventListener('DOMContentLoaded', () => {
  new Loadable();
});
```

#### 7. Create template in `templates/Loadable`. The name should be the same as your targeted model's name.

*Example for* `app/Models/MyLoadableModel.php`

```bash
touch themes/my_theme/templates/Loadable/MyLoadableModel.ss
```

(❗) The content in each template must start with a tag that has `data-loadable-list-item` attribute which represents a single loadable item

```html
<div data-loadable-list-item>
  <!-- my custom code goes here -->
</div>
```

*Real-case example:*

```html
<a href="{$Link}" data-loadable-list-item>
  $Image
  <h3>$Title</h3>
</a>
```

## Usage

To display loadable area use one of the examples below for further customization

#### Method 1 (quick preview)

```html
$Loadable(App\Models\MyLoadableModel)
```

#### Method 2 (basic)

```html
<% with $LoadableWith(App\Models\MyLoadableModel) %>
  <div data-loadable-area>
    $List
    <div>
      $Action
    </div>
  </div>
<% end_with %>
```

#### Method 3 (fully customizable)

```html
<% with $LoadableWith(App\Models\MyLoadableModel) %>
  <div data-loadable-area>
    <% with Data %>
    <div data-loadable-list data-loadable-remains="$CountRemains">
      <% loop List %>
        $loadableTemplate
      <% end_loop %>
    </div>
    <div>
      <button
        data-loadable-action
        data-loadable-params='{"search": "some search value"}'
        data-loadable-stock="{$LoadableObject}"
        data-loadable-scroll-offset="100"
        data-loading="false"
        class="btn btn-primary"
        type="button"
      >
        <span class="d-none spinner-border spinner-border-sm" aria-hidden="true"></span>
        <span role="status">Load more<span data-loadable-remaning></span></span>
      </button>
    </div>
    <% end_with %>
  </div>
<% end_with %>
```

## Events

Available callback events

```js
window.goldfinch.loadmore_before_callback = (action) => {
  console.log('loadmore before', action)

  let list = action.closest('[data-loadable-area]').children('[data-loadable-list]')

  // ..
}

window.goldfinch.loadmore_after_callback = (action) => {
  console.log('loadmore after', action)

  let list = action.closest('[data-loadable-area]').children('[data-loadable-list]')
  
  // ..
}
```

## License

The MIT License (MIT)
