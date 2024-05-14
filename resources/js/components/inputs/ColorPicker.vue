<template>
    <div class="form-group">
        <label v-if="label">
            {{ label }}
            <small
                v-if="labelNote"
                :class="labelNoteClass"
            >{{ labelNote }}</small>
        </label>

        <input
            v-model="inputValue"
            :type="type"
            :name="name"
            :style="componentStyle"
            class="form-control"
            :class="[ invalidClass ]"
            readonly
        >
        <div
            ref="elem"
            class="w-100"
        />

        <span
            v-if="isInvalid"
            class="invalid-feedback d-block"
        >{{ invalidMessage }}</span>
    </div>
</template>

<script>
import '@simonwep/pickr/dist/themes/monolith.min.css';
import Pickr from '@simonwep/pickr';

import InputMixin from './mixin.js';

export default {
    name: 'color-picker',

    mixins: [ InputMixin ],

    props: {
        type: {
            default: 'text',
            type: String
        },

        useAsButton: {
            default: false,
            type: Boolean
        },

        showAlways: {
            default: false,
            type: Boolean
        },

        inline: {
            default: false,
            type: Boolean
        },

        clear: {
            default: false,
            type: Boolean
        },

        save: {
            default: false,
            type: Boolean
        },

        appClass: {
            default: 'w-100 shadow-none',
            type: String
        }
    },

    data: () => ({
        elem: null,
        inputValue: '#FFFFFF'
    }),

    computed: {
        componentStyle() {
            let result = '';

            if (this.inputValue) {
                result = 'background-color:' + this.inputValue + ';';
            }

            return result;
        },

        /* Disable automatic changes due to conflict with other components using 3rd party plugin such as ckeditor */
        disableChangeEvent() {
            return true;
        }
    },

    watch: {
        modelValue(value) {
            this.inputValue = value;

            if (this.elem) {
                this.elem.setColor(value);
            }
        },

        inputValue() {
            this.$nextTick(() => {
                if (this.elem) {
                    this.inputChange(this.elem.getColor());
                }
            });
        }
    },

    mounted() {
        this.setup();
    },

    methods: {
        setup() {
            this.elem = Pickr.create({
                el: this.$refs.elem,
                theme: 'monolith', // or 'monolith', or 'nano'
                useAsButton: this.useAsButton,
                showAlways: this.showAlways,
                inline: this.inline,
                appClass: this.appClass,

                components: {

                    // Main components
                    preview: true,
                    opacity: false,
                    hue: true,

                    // Input / output Options
                    interaction: {
                        hex: false,
                        rgba: false,
                        hsla: false,
                        hsva: false,
                        cmyk: false,
                        input: true,
                        clear: this.clear,
                        save: this.save
                    }
                }
            });

            if (this.save) {
                this.elem.on('save', this.inputChange);
            } else {
                this.elem.on('changestop', this.changestop);
            }

        },

        inputChange(color) {
            let result = color;

            if (this.invalid) {
                this.invalid = false;
            }

            if (color) {
                result = color.toHEXA().toString();
            }

            this.$emit('input', result);
        },

        changestop(instance) {
            let color = instance.getColor();
            this.inputChange(color);
        }
    }
};
</script>

<style>
.pickr .pcr-button {
	width: 100%;
}
</style>