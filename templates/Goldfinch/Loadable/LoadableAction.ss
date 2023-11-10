<button
  data-loadable-action
  data-loadable-before-load="console.log('start loading')"
  data-loadable-after-load="console.log(response)"
  data-loadable-params='{"search": "some search value"}'
  data-loadable-stock="{$LoadableObject}"
  data-loadable-scroll-offset="10"
  data-loading="false"
  class="btn btn-primary"
  type="button"
>
  <span class="d-none spinner-border spinner-border-sm" aria-hidden="true"></span>
  <span role="status">Load more<span data-loadable-remaning></span></span>
</button>
