(function () {
  const SELECTOR = '.js-lang-dropdown';

  function isOpen(root) {
    return root.getAttribute('data-open') === 'true';
  }

  function setOpen(root, open) {
    root.setAttribute('data-open', open ? 'true' : 'false');
    const button = root.querySelector('.js-lang-dropdown-button');
    const menu = root.querySelector('.js-lang-dropdown-menu');
    if (button) button.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (menu) menu.hidden = !open;
  }

  function getItems(root) {
    return Array.from(root.querySelectorAll('.js-lang-dropdown-item'));
  }

  function focusItem(items, index) {
    if (!items.length) return;
    const clamped = Math.max(0, Math.min(index, items.length - 1));
    items.forEach((el, i) => el.setAttribute('tabindex', i === clamped ? '0' : '-1'));
    items[clamped].focus();
  }

  function openAndFocus(root, preferredIndex) {
    setOpen(root, true);
    const items = getItems(root);
    const activeIndex = items.findIndex((el) => el.getAttribute('aria-checked') === 'true');
    const index = typeof preferredIndex === 'number' ? preferredIndex : (activeIndex >= 0 ? activeIndex : 0);
    focusItem(items, index);
  }

  function close(root, returnFocus) {
    setOpen(root, false);
    if (returnFocus) {
      const button = root.querySelector('.js-lang-dropdown-button');
      if (button) button.focus();
    }
  }

  function closeAll(exceptRoot) {
    document.querySelectorAll(SELECTOR).forEach((root) => {
      if (exceptRoot && root === exceptRoot) return;
      close(root, false);
    });
  }

  function onButtonKeydown(e, root) {
    const key = e.key;

    if (key === 'ArrowDown' || key === 'Down') {
      e.preventDefault();
      closeAll(root);
      openAndFocus(root, 0);
      return;
    }

    if (key === 'ArrowUp' || key === 'Up') {
      e.preventDefault();
      closeAll(root);
      const items = getItems(root);
      openAndFocus(root, items.length ? items.length - 1 : 0);
      return;
    }

    if (key === 'Enter' || key === ' ') {
      e.preventDefault();
      if (isOpen(root)) {
        close(root, false);
      } else {
        closeAll(root);
        openAndFocus(root);
      }
      return;
    }

    if (key === 'Escape') {
      e.preventDefault();
      close(root, false);
    }
  }

  function onMenuKeydown(e, root) {
    const key = e.key;
    const items = getItems(root);
    const currentIndex = items.findIndex((el) => el === document.activeElement);

    if (key === 'Escape') {
      e.preventDefault();
      close(root, true);
      return;
    }

    if (key === 'ArrowDown' || key === 'Down') {
      e.preventDefault();
      focusItem(items, (currentIndex + 1) % items.length);
      return;
    }

    if (key === 'ArrowUp' || key === 'Up') {
      e.preventDefault();
      focusItem(items, (currentIndex - 1 + items.length) % items.length);
      return;
    }

    if (key === 'Home') {
      e.preventDefault();
      focusItem(items, 0);
      return;
    }

    if (key === 'End') {
      e.preventDefault();
      focusItem(items, items.length - 1);
      return;
    }

    if (key === 'Tab') {
      close(root, false);
    }
  }

  function init(root) {
    const button = root.querySelector('.js-lang-dropdown-button');
    const menu = root.querySelector('.js-lang-dropdown-menu');

    setOpen(root, false);

    if (button) {
      button.addEventListener('click', () => {
        if (isOpen(root)) {
          close(root, false);
        } else {
          closeAll(root);
          openAndFocus(root);
        }
      });

      button.addEventListener('keydown', (e) => onButtonKeydown(e, root));
    }

    if (menu) {
      menu.addEventListener('keydown', (e) => onMenuKeydown(e, root));
    }

    // Clicking an item should submit its form; close menus after click.
    getItems(root).forEach((item) => {
      item.addEventListener('click', () => {
        // Let form submit happen.
        closeAll();
      });
    });
  }

  // Init all dropdowns
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(SELECTOR).forEach(init);

    document.addEventListener('click', (e) => {
      const target = e.target;
      if (!(target instanceof Element)) return;
      const root = target.closest(SELECTOR);
      if (root) return;
      closeAll();
    });

    window.addEventListener('resize', () => closeAll());
  });
})();
