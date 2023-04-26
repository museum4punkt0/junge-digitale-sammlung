(function() {
  "use strict";
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
  const _sfc_main = {
    props: {
      role: Object,
      roles: Array,
      search: String,
      title: String,
      users: Object
    },
    computed: {
      items() {
        return this.users.data.map((user) => {
          user.options = this.$dropdown(user.link);
          return user;
        });
      }
    },
    methods: {
      paginate(pagination) {
        this.$reload({
          query: {
            page: pagination.page
          }
        });
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-inside", [_c("k-view", { staticClass: "k-users-view" }, [_c("k-header", { scopedSlots: _vm._u([{ key: "left", fn: function() {
      return [_c("k-button-group", { attrs: { "buttons": [
        {
          disabled: _vm.$permissions.users.create === false,
          text: "Admin Konto anlegen",
          icon: "add",
          click: () => _vm.$dialog("users/create")
        },
        {
          disabled: _vm.$permissions.users.create === false,
          text: "Temp. Konto anlegen",
          icon: "clock",
          click: () => _vm.$dialog("users/createTempUser")
        }
      ] } })];
    }, proxy: true }, _vm.roles.length > 1 ? { key: "right", fn: function() {
      return [_c("k-button-group", [_c("k-dropdown", [_c("k-button", { attrs: { "responsive": true, "text": `${_vm.$t("role")}: ${_vm.role ? _vm.role.title : _vm.$t("role.all")}`, "icon": "funnel" }, on: { "click": function($event) {
        return _vm.$refs.roles.toggle();
      } } }), _c("k-dropdown-content", { ref: "roles", attrs: { "align": "right" } }, [_c("k-dropdown-item", { attrs: { "icon": "bolt", "link": "/users" } }, [_vm._v(" " + _vm._s(_vm.$t("role.all")) + " ")]), _c("hr"), _vm._l(_vm.roles, function(roleFilter) {
        return _c("k-dropdown-item", { key: roleFilter.id, attrs: { "link": "/users/?role=" + roleFilter.id, "icon": "bolt" } }, [_vm._v(" " + _vm._s(roleFilter.title) + " ")]);
      })], 2)], 1)], 1)];
    }, proxy: true } : null], null, true) }, [_vm._v(" Konten ")]), _vm.users.data.length > 0 ? [_c("k-collection", { attrs: { "items": _vm.items, "pagination": _vm.users.pagination }, on: { "paginate": _vm.paginate } })] : _vm.users.pagination.total === 0 ? [_c("k-empty", { attrs: { "icon": "users" } }, [_vm._v(" " + _vm._s(_vm.$t("role.empty")) + " ")])] : _vm._e()], 2)], 1);
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
  __component__.options.__file = "/Users/santiagoduque/Documents/WEBPROJECTS/mamp/junge-digitale-sammlung-website/www/site/plugins/2av-temporary-users/src/components/UsersView.vue";
  const UsersView = __component__.exports;
  panel.plugin("2av/users", {
    components: {
      "k-users-view": UsersView
    }
  });
})();
