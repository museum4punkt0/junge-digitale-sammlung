panel.plugin("2av/blocks-factory", {
  blocks: {

    // TOAST
    toast: {
      computed: {
        textField() {
          return this.field("text");
        }
      },
      template: `
            <div :class="'k-innerblock-type-toast toast-' + content.toasttype">
              <k-writer
                class="label"
                ref="textbox"
                :marks="textField.marks"
                :value="content.text"
                :placeholder="textField.placeholder || 'Enter text...'"
                @input="update({ text: $event })"
              />
              <k-icon
                v-if="content.type !== 'neutral'"
                class="k-block-type-toast-icon"
                :type="content.toasttype"
              />
            </div>
          `
    },

    // ACCORDION ELEMENT
    accordion_element: {
      computed: {
        summaryField() {
          return this.field("summary");
        },
        detailsField() {
          return this.field("details");
        }
      },
      template: `
          <div @dblclick="open">
            <details>
              <summary>
                <k-writer
                  ref="summary"
                  :inline="true"
                  marks="false"
                  :placeholder="summaryField.placeholder || 'Add a summary…'"
                  :value="content.summary"
                  @input="update({ summary: $event })"
                />
              </summary>
              <k-writer
                  ref="details"
                  :inline="detailsField.inline || false"
                  :marks="detailsField.marks"
                  :value="content.details"
                  :placeholder="detailsField.placeholder || 'Add some details'"
                  @input="update({ details: $event })"
                />
            </details>
          </div>
        `
    },

    // ACCORDION
    accordion: {
      computed: {
      },

      template: `
      <div @dblclick="open">
       
        <div v-if="content.elements.length">
          <details
            class="k-block-type-accset-item"
            v-for="(item, index) in content.elements"
            :key="index"
          >
            <summary v-html="item.content.summary"></summary>
              <div v-html="item.content.details"></div>
            </div>
          </details>
        </div>
        <div v-else>No items yet</div>
      </div>
    `},

    // CARD
    card: {
      computed: {
        headingField() {
          return this.field("heading");
        },
        textField() {
          return this.field("text");
        },
        heading() {
          return this.content.heading || 'Heading...';
        },
        image() {
          return this.content.image[0] || {};
        },
        text() {
          return this.content.text || 'Text...';
        }
      },
      template: `
        <div @dblclick="open">
          <k-aspect-ratio
            class="k-block-type-card-image"
            cover="true"
            ratio="1/1"
          >
            <img
              v-if="image.url"
              :src="image.url"
              alt=""
            >
          </k-aspect-ratio>
          <h2 class="k-block-type-card-heading">
          <k-writer
            ref="input"
            :inline="headingField.inline"
            :marks="headingField.marks"
            :placeholder="headingField.placeholder"
            :value="content.heading"
            @input="update({ heading: $event })"
          />
          </h2>
          <k-writer
            ref="input"
            :inline="textField.inline"
            :marks="textField.marks"
            :nodes="textField.nodes"
            :placeholder="textField.placeholder"
            :value="content.text"
            class="k-block-type-card-text"
            @input="update({ text: $event })"
          />
        </div>
      `
    },
  }
});