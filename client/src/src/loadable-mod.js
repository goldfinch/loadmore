import $ from 'jquery';
import axios from 'axios';

class Loadable {
  constructor() {
    if (!window.axios) {
      window.axios = axios;
    }

    this.initAxios();

    this.loadableAreas();
  }

  initAxios() {
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    const token = document.head.querySelector(
      'meta[name="csrf-token"]',
    ).content;

    if (token) {
      window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    } else {
      console.error('CSRF token not found!');
    }
  }

  updateRemaining(countRemains, action) {
    const labelRemaning = action.find('[data-loadable-remaning]');

    if (labelRemaning.length) {
      if (countRemains > 0) {
        labelRemaning.html(` (${countRemains})`);
      } else {
        labelRemaning.remove();
      }
    }
  }

  query(action) {
    if (
      action.attr('data-loading') === 'true' ||
      action.attr('data-loadable-disabled') === 'true'
    ) {
      return;
    }

    this.actionInProgress(action, true);

    let params = action.attr('data-loadable-params');
    const stock = action.attr('data-loadable-stock');
    const substance = action.attr('data-loadable-substance');
    const substanceId = action.attr('data-loadable-substance-id');
    const scrollOffset = action.attr('data-loadable-scroll-offset');
    const area = action.closest('[data-loadable-area]');
    const list = area.find('[data-loadable-list]');
    const currentItemsCount = list.find('[data-loadable-list-item]').length;
    const countRemains = list.attr('data-loadable-remains');

    // callback
    if (window.goldfinch && window.goldfinch.loadmore_before_callback) {
      window.goldfinch.loadmore_before_callback(action);
    }

    params = JSON.parse(params);
    params.start = currentItemsCount;
    params.urlparams = window.location.search;

    if (substance && substance != '') {
      params.substance = substance;
    }
    if (substanceId && substanceId != '') {
      params.substance_id = substanceId;
    }

    window.axios
      .post(`api/loadable/fetch/${stock}`, params)
      .then((response) => {
        this.actionInProgress(action, false);

        if (response.data === false) {
          this.disableLoadaction(action);
        } else {
          const previousAppendedItem = list.find(
            '[data-loadable-first-appended]',
          );

          if (previousAppendedItem.length) {
            previousAppendedItem.removeAttr('data-loadable-first-appended');
          }

          const rootList = $('<div>')
            .html(response.data)
            .children('[data-loadable-list]');
          const countRemains = rootList.attr('data-loadable-remains');
          const onlyListItems = $('<div>')
            .html(response.data)
            .children('[data-loadable-list]');

          this.updateRemaining(countRemains, action);

          if (countRemains <= 0) {
            this.disableLoadaction(action);
          }

          onlyListItems
            .find('[data-loadable-list-item]')
            .first()
            .attr('data-loadable-first-appended', '');
          list.append(onlyListItems.html());

          if (scrollOffset) {
            const isFirstAppended = list.find('[data-loadable-first-appended]');
            if (isFirstAppended.length) {
              const firstAppended = isFirstAppended.first().get()[0];
              // firstAppended.scrollIntoView({block: "start", behavior: "smooth"});

              window.scrollTo({
                top: firstAppended.offsetTop - parseInt(scrollOffset),
                behavior: 'smooth',
              });
            }
          }

          // callback
          if (window.goldfinch && window.goldfinch.loadmore_after_callback) {
            window.goldfinch.loadmore_after_callback(action);
          }
        }
      })
      .catch(function (error) {
        console.log('XHR error', error);

        this.actionInProgress(action, false);
      });
  }

  disableLoadaction(action) {
    action.css({ opacity: 0.25, 'pointer-events': 'none' });
    action.attr('data-loadable-disabled', true);
    action.attr('disabled', true);
  }

  actionInProgress(action, state) {
    action.attr('data-loading', state);

    const spinner = action.find('.spinner-border');
    const label = action.find('[role="status"]');

    if (state) {
      spinner.removeClass('d-none');
      label.attr('data-label', label.html());
      label.html('Loading...');
    } else {
      spinner.addClass('d-none');
      label.html(label.attr('data-label'));
    }
  }

  loadableAreas() {
    const loadmore = $('[data-loadable-area]');

    if (loadmore.length) {
      loadmore.each((i, e) => {
        const action = $(e).find('[data-loadable-action]');

        if (action.length) {
          this.updateRemaining(
            loadmore
              .children('[data-loadable-list]')
              .attr('data-loadable-remains'),
            action,
          );

          action.on('click', (e) => {
            this.query(action);
          });
        }
      });
    }
  }
}

export default Loadable;
