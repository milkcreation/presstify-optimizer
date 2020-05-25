'use strict';

let preloadImage = function (img) {
      const src = img.getAttribute('data-src');
      const srcset = img.getAttribute('data-srcset');

      if (!src && !srcset) {
        return;
      }

      if (src) {
        img.src = src;
      }

      if (srcset) {
        img.srcset = srcset;
      }
    },
    intersectionObserver = new IntersectionObserver(function (entries, self) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          preloadImage(entry.target);
          self.unobserve(entry.target);
        }
      });
    }, {rootMargin: '0px 0px 50px 0px', threshold: 0}),
    mutationObserver = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(node => {
          if (typeof node.querySelectorAll === "function") {
            const images = node.querySelectorAll('[data-src]');
            images.forEach(image => {
              intersectionObserver.observe(image);
            });
          }
        });
      });
    });

document.querySelectorAll('[data-src]').forEach(image => {
  intersectionObserver.observe(image);
});

mutationObserver.observe(document.documentElement, {childList: true, subtree: true});