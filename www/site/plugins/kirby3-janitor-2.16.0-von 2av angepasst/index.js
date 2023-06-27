(function() {
  "use strict";
  const Janitor_vue_vue_type_style_index_0_lang = "";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        );
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const STORAGE_ID = "janitor.runAfterAutosave";
  const _sfc_main = {
    props: {
      generator: String,
      reload: String,
      label: String,
      progress: String,
      job: String,
      cooldown: Number,
      status: String,
      data: String,
      pageURI: String,
      clipboard: Boolean,
      unsaved: Boolean,
      autosave: Boolean,
      intab: Boolean,
      confirm: String,
      icon: {
        type: [Boolean, String],
        default: false
      }
    },
    data() {
      return {
        button: {
          label: null,
          state: null
        },
        downloadRequest: null,
        clipboardRequest: null,
        urlRequest: null,
        isUnsaved: false,
        amount: null,
        amountLeaders: null,
        icons: {
          "is-running": "janitorLoader",
          "is-success": "check",
          "has-error": "alert"
        }
      };
    },
    computed: {
      id() {
        var _a;
        return "janitor-" + this.hashCode(this.job + ((_a = this.button.label) != null ? _a : "") + this.pageURI);
      },
      hasChanges() {
        return this.$store.getters["content/hasChanges"]();
      },
      currentIcon() {
        var _a;
        return (_a = this.icons[this.status]) != null ? _a : this.icon;
      }
    },
    created() {
      this.$events.$on(
        "model.update",
        () => sessionStorage.getItem(STORAGE_ID) && location.reload()
      );
      if (sessionStorage.getItem(STORAGE_ID) === this.id) {
        sessionStorage.removeItem(STORAGE_ID);
        this.runJanitor();
      }
    },
    methods: {
      updateAmount(str) {
        if (str.amount < 0) {
          this.amount = 0;
        } else if (str.amount > 30) {
          this.amount = 30;
        } else {
          this.amount = str.amount;
        }
      },
      updateAmountLeader(str) {
        if (str.amountLeaders < 0) {
          this.amountLeaders = 0;
        } else if (str.amountLeaders > 3) {
          this.amountLeaders = 3;
        } else {
          this.amountLeaders = str.amountLeaders;
        }
      },
      hashCode(str) {
        let hash = 0;
        if (str.length === 0) {
          return hash;
        }
        for (const i of str) {
          hash = (hash << 5) - hash + str.charCodeAt(i);
          hash = hash & hash;
        }
        return hash;
      },
      async runJanitor() {
        if (this.confirm && !window.confirm(this.confirm)) {
          return;
        }
        if (this.autosave && this.hasChanges) {
          const saveButton = document.querySelector(
            ".k-panel .k-form-buttons .k-view"
          ).lastChild;
          if (saveButton) {
            this.isUnsaved = false;
            sessionStorage.setItem(STORAGE_ID, this.id);
            this.simulateClick(saveButton);
            return;
          }
        }
        if (this.clipboard) {
          this.clipboardRequest = this.data;
          this.button.label = this.progress;
          this.button.state = "is-success";
          setTimeout(this.resetButton, this.cooldown);
          this.$nextTick(() => {
            this.copyToClipboard(this.data);
          });
          return;
        }
        if (this.clipboardRequest) {
          await this.copyToClipboard(this.clipboardRequest);
          this.resetButton();
          this.clipboardRequest = null;
          return;
        }
        if (this.status) {
          return;
        }
        let url = this.job + "/" + encodeURIComponent(this.pageURI);
        if (this.generator) {
          if (!this.amount)
            this.amount = 0;
          if (!this.amountLeaders)
            this.amountLeaders = 0;
          url = url + "/" + encodeURIComponent(this.amount + "&" + this.amountLeaders);
        }
        if (this.data) {
          url = url + "/" + encodeURIComponent(this.data);
        }
        this.getRequest(url);
      },
      async getRequest(url) {
        var _a;
        this.button.label = (_a = this.progress) != null ? _a : `${this.label} \u2026`;
        this.button.state = "is-running";
        const { label, status, reload, href, download, clipboard } = await this.$api.get(url);
        if (label) {
          this.button.label = label;
        }
        if (status) {
          this.button.state = status === 200 ? "is-success" : "has-error";
        } else {
          this.button.state = "has-response";
        }
        if (reload) {
          location.reload();
        }
        if (href) {
          if (this.intab) {
            this.urlRequest = href;
            this.$nextTick(() => {
              this.simulateClick(this.$refs.tabAnchor);
            });
          } else {
            location.href = href;
          }
        }
        if (download) {
          this.downloadRequest = download;
          this.$nextTick(() => {
            this.simulateClick(this.$refs.downloadAnchor);
          });
        }
        if (clipboard) {
          this.clipboardRequest = clipboard;
        } else {
          setTimeout(this.resetButton, this.cooldown);
        }
      },
      resetButton() {
        this.button.label = null;
        this.button.state = null;
        if (this.reload) {
          location.reload();
        }
      },
      simulateClick(element) {
        const evt = new MouseEvent("click", {
          bubbles: true,
          cancelable: true,
          view: window
        });
        element.dispatchEvent(evt);
      },
      async copyToClipboard(content) {
        try {
          await navigator.clipboard.writeText(content);
        } catch (err) {
          console.error("navigator.clipboard is not available");
        }
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "k-grid" }, [_vm.generator ? _c("span", { staticClass: "k-grid" }, [_c("div", { staticClass: "k-column" }, [_c("k-number-field", { attrs: { "novalidate": true, "placeholder": 0, "value": 0, "max": 3, "min": 0, "name": "amountLeaders", "help": "Leiter:innen (Min: 0, Max: 3)", "label": "Leiter:innen Anzahl" }, on: { "input": function($event) {
      return _vm.updateAmountLeader({ amountLeaders: $event });
    } }, model: { value: _vm.amountLeaders, callback: function($$v) {
      _vm.amountLeaders = $$v;
    }, expression: "amountLeaders" } })], 1), _c("div", { staticClass: "k-column" }, [_c("k-number-field", { attrs: { "novalidate": true, "placeholder": 0, "value": 0, "max": 30, "min": 0, "name": "amount", "help": "Teilnehmer:innen (Min: 0, Max: 30)", "label": "Teilnehmer:innen Anzahl" }, on: { "input": function($event) {
      return _vm.updateAmount({ amount: $event });
    } }, model: { value: _vm.amount, callback: function($$v) {
      _vm.amount = $$v;
    }, expression: "amount" } })], 1)]) : _vm._e(), _c("div", { staticClass: "k-column janitor-wrapper" }, [_c("k-button", { class: ["janitor", _vm.button.state], attrs: { "id": _vm.id, "icon": _vm.currentIcon, "job": _vm.job, "disabled": !_vm.isUnsaved && _vm.hasChanges }, on: { "click": _vm.runJanitor } }, [_vm._v(" " + _vm._s(_vm.button.label || _vm.label) + " ")]), _c("a", { directives: [{ name: "show", rawName: "v-show", value: _vm.downloadRequest, expression: "downloadRequest" }], ref: "downloadAnchor", staticClass: "visually-hidden", attrs: { "href": _vm.downloadRequest, "download": "" } }), _c("a", { directives: [{ name: "show", rawName: "v-show", value: _vm.urlRequest, expression: "urlRequest" }], ref: "tabAnchor", staticClass: "visually-hidden", attrs: { "href": _vm.urlRequest, "target": "_blank" } })], 1)]);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    null,
    null,
    null
  );
  __component__.options.__file = "/Users/santiagoduque/Documents/WEBPROJECTS/mamp/scd-junge_digitale_sammlung/www/site/plugins/kirby3-janitor-2.16.0-von 2av angepasst/src/components/fields/Janitor.vue";
  const Janitor = __component__.exports;
  window.panel.plugin("bnomei/janitor", {
    fields: {
      janitor: Janitor
    },
    icons: {
      janitorLoader: '<g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="1.75"><circle cx="7" cy="7" r="7.2" stroke="#000" stroke-opacity=".2"/><path d="M14.2,7c0-4-3.2-7.2-7.2-7.2" stroke="#000"><animateTransform attributeName="transform" type="rotate" from="0 7 7" to="360 7 7" dur="1s" repeatCount="indefinite"/></path></g></g>'
    }
  });
})();
