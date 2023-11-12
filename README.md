app/_config/loadable.yml
```
---
Name: app-loadable
---

Goldfinch\Loadable\Loadable:
  loadable:
    App\Models\MyModel...:
      initial_loaded: 10
      per_each_load: 10

```

templates/{theme}/Loadable/MyModel....ss


```
public static function loadable($params, $request, $config)
{
    return Project::get(); // ->limit($params['limit'], $params['start']);
}

public function loadableTemplate()
{
    return $this->renderWith('Loadable/ProjectItem');
}
```

APP_KEY=""
```
php taz generate:crypto-key
```

Add to header:
```
<meta name="csrf-param" content="authenticity_token">
<meta name="csrf-token" content="{$SecurityID}">
```



```
<div class="text-center my-5">

  <!-- Method 1 -->
  $Loadable(App\Models\MyModel...)

  <!-- Method 2 -->
  <% with $LoadableWith(App\Models\MyModel...) %>
    <div data-loadable-area>
      $List
      <div>
        $Action
      </div>
    </div>
  <% end_with %>

  <!-- Method 3 -->
  <% with $LoadableWith(App\Models\MyModel...) %>
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
          data-loadable-before-load="console.log('start loading')"
          data-loadable-after-load="console.log(response)"
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

</div>
```

app.js
```
import Loadable from '..../vendor/goldfinch/loadable/client/src/src/loadable-mod';

document.addEventListener('DOMContentLoaded', () => {
  new Loadable();
});
```
