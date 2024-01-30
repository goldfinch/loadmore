<div data-loadable-list data-loadable-remains="$CountRemains">
  <% if List %>
    <% loop List %>
      $loadableTemplate
    <% end_loop %>
  <% else %>
    <p>Sorry, there are no items that match your request</p>
  <% end_if %>
</div>
