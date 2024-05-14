/* eslint-disable vue/require-name-property */
// @vue/component
export default {

    model: {
        prop: 'modelValue',
        event: 'input'
    },

    props: {
        modelValue: {
            default: null,
            type: String
        },

        name: {
            default: null,
            type: String
        },

        label: {
            default: null,
            type: String
        },

        labelNote: {
            default: null,
            type: String
        },

        labelNoteClass: {
            default: 'text-danger',
            type: String
        },

        placeholder: {
            default: null,
            type: String
        },

        emptyText: {
            default: null,
            type: String
        },

        disabled: {
            default: false,
            type: Boolean
        },

        /* Used to display error messages because some name has inputname[] which cannot be matched with html name */
        error: {
            default: null,
            type: String
        },

        hint: {
            default: null,
            type: String
        },

        validatable: {
            default: true,
            type: Boolean
        }
    },

	data: () => ({
        inputValue: null,
        invalid: false,
        invalidText: null,

        hintElem: null
    }),

    computed: {
        /* Determine whether input is invalid */
        isInvalid() {
            return this.invalid;
        },

        /* Invalid message string */
        invalidMessage() {
            return this.invalidText || this.error;
        },

        /**
         * Add the native event listeners to another dom element
         * Reference: https://vuejs.org/v2/guide/components-custom-events.html#Binding-Native-Events-to-Components
         * @return Object event listener object
         */
        inputListeners() {
            // `Object.assign` merges objects together to form a new object
            return Object.assign({},
                // We add all the listeners from the parent
                this.$listeners,
                // Then we can add custom listeners or override the
                // behavior of some listeners.
                this.inputCustomListeners
            );
        },

        /**
         * Override native event listeners
         * Reference: https://vuejs.org/v2/guide/components-custom-events.html#Binding-Native-Events-to-Components
         * @return Object event listener object
         */
        inputCustomListeners() {
            return {};
        },

        invalidClass() {
            return this.isInvalid ? 'is-invalid' : '';
        },

        /* Disable automatic changes due to conflict with other components using 3rd party plugin such as ckeditor */
        disableChangeEvent() {
            return false;
        }
    },

    watch: {
        modelValue(value) {
            this.inputValue = value;
        },

        inputValue() {
            if (! this.disableChangeEvent) {
                this.inputChange();
            }
        },

        error(value) {
            if (value) {
                this.invalid = true;
            } else {
                this.invalid = false;
            }
        }
    },

    mounted() {
        this.setupHint();
        if (! this.disableChangeEvent) {
            this.inputValue = this.modelValue;
        }
    },

    methods: {
        inputChange() {
            if (this.invalid) {
                this.invalid = false;
            }

            this.$emit('input', this.inputValue);
        },

        setupHint() {
            if (this.hint) {
                this.hintElem = $(this.$refs.hint);
                this.hintElem.tooltip({
                    placement: 'right',
                    title: this.hint
                });
            }
        }
    }
};