<button
  data-loadable-action
  data-loadable-params='{"search": "some search value"}'
  data-loadable-stock="{$LoadableObject}"
  data-loadable-scroll-offset="10"
  data-loading="false"
  class="btn btn-primary"
  type="button"
  <% if not CountRemains %>
  data-loadable-disabled="true"
  disabled="disabled"
  style="opacity: 0.25; pointer-events: none;"
  <% end_if %>
>
  <span
    class="d-none spinner-border spinner-border-sm"
    aria-hidden="true"
  ></span>
  <span role="status">Load more<span data-loadable-remaning></span></span>
</button>
