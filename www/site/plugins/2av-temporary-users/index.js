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
  const _sfc_main$2 = {
    props: {
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
  var _sfc_render$2 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-inside", [_c("k-view", { staticClass: "k-users-view" }, [_c("k-header", { scopedSlots: _vm._u([{ key: "left", fn: function() {
      return [_c("k-button-group", { attrs: { "buttons": [
        {
          disabled: _vm.$permissions.users.create === false,
          text: _vm.$t("user.create"),
          icon: "add",
          click: () => _vm.$dialog("temporaryusers/create")
        }
      ] } })];
    }, proxy: true }]) }, [_vm._v(" Temporary Users ")]), _vm.users.data.length > 0 ? [_c("k-collection", { attrs: { "items": _vm.items, "pagination": _vm.users.pagination }, on: { "paginate": _vm.paginate } })] : _vm.users.pagination.total === 0 ? [_c("k-empty", { attrs: { "icon": "users" } }, [_vm._v(" " + _vm._s(_vm.$t("role.empty")) + " ")])] : _vm._e()], 2)], 1);
  };
  var _sfc_staticRenderFns$2 = [];
  _sfc_render$2._withStripped = true;
  var __component__$2 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$2,
    _sfc_render$2,
    _sfc_staticRenderFns$2,
    false,
    null,
    null,
    null,
    null
  );
  __component__$2.options.__file = "/Users/santiagoduque/Documents/WEBPROJECTS/mamp/scd-junge_digitale_sammlung/www/site/plugins/2av-temporary-users/src/components/TempUsers.vue";
  const TempUsers = __component__$2.exports;
  const _sfc_main$1 = {
    props: {
      blueprint: String,
      next: Object,
      prev: Object,
      permissions: {
        type: Object,
        default() {
          return {};
        }
      },
      lock: {
        type: [Boolean, Object]
      },
      model: {
        type: Object,
        default() {
          return {};
        }
      },
      tab: {
        type: Object,
        default() {
          return {
            columns: []
          };
        }
      },
      tabs: {
        type: Array,
        default() {
          return [];
        }
      }
    },
    computed: {
      id() {
        return this.model.link;
      },
      isLocked() {
        var _a;
        return ((_a = this.lock) == null ? void 0 : _a.state) === "lock";
      },
      protectedFields() {
        return [];
      }
    },
    watch: {
      "model.id": {
        handler() {
          this.content();
        },
        immediate: true
      }
    },
    created() {
      this.$events.$on("model.reload", this.reload);
      this.$events.$on("keydown.left", this.toPrev);
      this.$events.$on("keydown.right", this.toNext);
    },
    destroyed() {
      this.$events.$off("model.reload", this.reload);
      this.$events.$off("keydown.left", this.toPrev);
      this.$events.$off("keydown.right", this.toNext);
    },
    methods: {
      content() {
        this.$store.dispatch("content/create", {
          id: this.id,
          api: this.id,
          content: this.model.content,
          ignore: this.protectedFields
        });
      },
      async reload() {
        await this.$reload();
        this.content();
      },
      toPrev(e) {
        if (this.prev && e.target.localName === "body") {
          this.$go(this.prev.link);
        }
      },
      toNext(e) {
        if (this.next && e.target.localName === "body") {
          this.$go(this.next.link);
        }
      }
    }
  };
  const _sfc_render$1 = null;
  const _sfc_staticRenderFns$1 = null;
  var __component__$1 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$1,
    _sfc_render$1,
    _sfc_staticRenderFns$1,
    false,
    null,
    null,
    null,
    null
  );
  __component__$1.options.__file = "/Users/santiagoduque/Documents/WEBPROJECTS/mamp/scd-junge_digitale_sammlung/www/site/plugins/2av-temporary-users/src/components/ModelView.vue";
  const ModelView = __component__$1.exports;
  const TempUser_vue_vue_type_style_index_0_lang = "";
  const _sfc_main = {
    extends: ModelView,
    computed: {
      avatarOptions() {
        return [
          {
            icon: "upload",
            text: this.$t("change"),
            click: () => this.$refs.upload.open()
          },
          {
            icon: "trash",
            text: this.$t("delete"),
            click: this.deleteAvatar
          }
        ];
      },
      buttons() {
        return [
          {
            icon: "email",
            text: `${this.$t("email")}: ${this.model.email}`,
            disabled: !this.permissions.changeEmail || this.isLocked,
            click: () => this.$dialog(this.id + "/changeEmail")
          },
          {
            icon: "bolt",
            text: `${this.$t("role")}: ${this.model.role}`,
            disabled: !this.permissions.changeRole || this.isLocked,
            click: () => this.$dialog(this.id + "/changeRole")
          },
          {
            icon: "globe",
            text: `${this.$t("language")}: ${this.model.language}`,
            disabled: !this.permissions.changeLanguage || this.isLocked,
            click: () => this.$dialog(this.id + "/changeLanguage")
          }
        ];
      },
      uploadApi() {
        return this.$urls.api + "/" + this.id + "/avatar";
      }
    },
    methods: {
      async deleteAvatar() {
        await this.$api.users.deleteAvatar(this.model.id);
        this.avatar = null;
        this.$store.dispatch("notification/success", ":)");
        this.$reload();
      },
      onAvatar() {
        if (this.model.avatar) {
          this.$refs.picture.toggle();
        } else {
          this.$refs.upload.open();
        }
      },
      uploadedAvatar() {
        this.$store.dispatch("notification/success", ":)");
        this.$reload();
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-inside", { scopedSlots: _vm._u([{ key: "footer", fn: function() {
      return [_c("k-form-buttons", { attrs: { "lock": _vm.lock } })];
    }, proxy: true }]) }, [_c("div", { staticClass: "k-tempuser-view", attrs: { "data-locked": _vm.isLocked, "data-id": _vm.model.id, "data-template": _vm.blueprint } }, [_c("div", { staticClass: "k-user-profile" }, [_c("k-view", [_c("k-dropdown", [_c("k-button", { staticClass: "k-user-view-image", attrs: { "tooltip": _vm.$t("avatar"), "disabled": _vm.isLocked }, on: { "click": _vm.onAvatar } }, [_vm.model.avatar ? _c("k-image", { attrs: { "cover": true, "src": _vm.model.avatar, "ratio": "1/1" } }) : _c("k-icon", { attrs: { "back": "gray-900", "color": "gray-200", "type": "user" } })], 1), _vm.model.avatar ? _c("k-dropdown-content", { ref: "picture", attrs: { "options": _vm.avatarOptions } }) : _vm._e()], 1), _c("k-button-group", { attrs: { "buttons": _vm.buttons } })], 1)], 1), _c("k-view", [_c("k-header", { attrs: { "editable": _vm.permissions.changeName && !_vm.isLocked, "tab": _vm.tab.name, "tabs": _vm.tabs }, on: { "edit": function($event) {
      return _vm.$dialog(_vm.id + "/changeName");
    } }, scopedSlots: _vm._u([{ key: "left", fn: function() {
      return [_c("k-button-group", [_c("k-dropdown", { staticClass: "k-user-view-options" }, [_c("k-button", { attrs: { "disabled": _vm.isLocked, "text": _vm.$t("settings"), "icon": "cog" }, on: { "click": function($event) {
        return _vm.$refs.settings.toggle();
      } } }), _c("k-dropdown-content", { ref: "settings", attrs: { "options": _vm.$dropdown(_vm.id) } })], 1), _c("k-languages-dropdown")], 1)];
    }, proxy: true }]) }, [!_vm.model.name || _vm.model.name.length === 0 ? _c("span", { staticClass: "k-user-name-placeholder" }, [_vm._v(" " + _vm._s(_vm.$t("name")) + " \u2026 ")]) : [_vm._v(" " + _vm._s(_vm.model.name) + " ")]], 2), _c("k-sections", { attrs: { "blueprint": _vm.blueprint, "empty": _vm.$t("user.blueprint", { blueprint: _vm.$esc(_vm.blueprint) }), "lock": _vm.lock, "parent": _vm.id, "tab": _vm.tab } }), _c("k-upload", { ref: "upload", attrs: { "url": _vm.uploadApi, "multiple": false, "accept": "image/*" }, on: { "success": _vm.uploadedAvatar } })], 1)], 1)]);
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
  __component__.options.__file = "/Users/santiagoduque/Documents/WEBPROJECTS/mamp/scd-junge_digitale_sammlung/www/site/plugins/2av-temporary-users/src/components/TempUser.vue";
  const TempUser = __component__.exports;
  panel.plugin("2av/temporaryusers", {
    components: {
      temporaryusers: TempUsers,
      temporaryuser: TempUser
    }
  });
})();
