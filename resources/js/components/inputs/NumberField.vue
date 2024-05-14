<template>
    <div class="form-group">
        <label v-if="label || labelNote">
            {{ label }}
            <span
                v-if="required"
                class="text-danger"
            >*</span>
            <small
                v-if="labelNote"
                :class="labelNoteClass"
            >
                {{ labelNote }}
            </small>
        </label>

        <div :class="{ 'input-group': $slots['append'] || $slots['prepend'] }">
            <div
                v-if="$slots['prepend']"
                class="input-group-prepend"
            >
                <slot name="prepend" />
            </div>

            <input
                v-model="inputValue"
                v-bind="$attrs"
                type="text"
                :name="name"
                class="form-control"
                :class="[ invalidClass ]"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                v-on="inputListeners"
            >

            <div
                v-if="$slots['append']"
                class="input-group-append"
            >
                <slot name="append" />
            </div>
        </div>

        <span
            v-if="isInvalid"
            class="invalid-feedback d-block"
        >{{ invalidMessage }}</span>
    </div>
</template>

<script>
import InputMixin from './mixin.js';
import NumberMixin from 'Mixins/number.js';

export default {
    name: 'number-field',

    mixins: [ InputMixin, NumberMixin ],

    props: {
        modelValue: {
            default: null,
            type: [ Number, String ]
        },

        type: {
            default: 'number',
            type: String
        },

        min: {
            default: null,
            type: [ Number, String ]
        },

        max: {
            default: null,
            type: [ Number, String ]
        },

        required: {
            default: false,
            type: Boolean
        },

        readonly: {
            default: false,
            type: Boolean
        },
    },

    data: () => ({
        pattern: /^[0-9]*\.?[0-9]*$/,
        format: /^\d*\.?\d*$/
    }),

    computed: {
        inputCustomListeners() {
            return {
                keypress: (event) => {
                    this.validate(event);
                },

                paste: (event) => {
                    this.validate(event);
                },

                input: () => {
                    this.inputChange();
                },

                blur: () => {
                    this.validateFormat();
                }
            };
        }
    },

    mounted() {
        this.setup(this.type);
    },

    methods: {
        setup(type) {
            switch (type) {
                case 'number':
                    this.pattern = /^[0-9]*\.?[0-9]*$/;
                    this.format = /^\d*\.?\d*$/;
                    break;
                case 'integer':
                    this.pattern = /^[0-9]+$/;
                    this.format = /^[0-9]+$/;
                    break;
                case 'real-integer':
                    this.pattern = /^[0-9-]*$/;
                    this.format = /^[-]?[0-9]+$/;
                    break;
            }
        },

        /* validate format on blur */
        validate(event) {
            console.log(event);
            let key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (event.which != 13 || event.keyCode != 13) {
                if (!this.pattern.test(key)) {
                    event.preventDefault();
                }
            }
        },

        /* validate format on blur */
        validateFormat() {
            if (this.inputValue) {
                if (! this.format.test(this.inputValue)) {
                    switch (this.type) {
                        case 'number':
                            this.inputValue = parseFloat(this.inputValue);
                            this.inputChange();
                            break;
                        case 'real-integer':
                            this.inputValue = parseInt(this.inputValue);
                            this.inputChange();
                            break;
                    }
                }

                if (this.type == 'integer') {
                    this.inputValue = Math.floor(this.inputValue);
                }

                if (this.isNumeric(this.min) && this.isNumeric(this.max)) {
                    if (this.inputValue < this.min || this.inputValue > this.max) {
                        if (this.inputValue < this.min) {
                            this.inputValue = this.min;
                        } else if (this.inputValue > this.max) {
                            this.inputValue = this.max;
                        }
                    }
                } else if (this.isNumeric(this.min)) {
                    if (this.inputValue < this.min) {
                        this.inputValue = this.min;
                    }
                } else if (this.isNumeric(this.max)) {
                    if (this.inputValue > this.max) {
                        this.inputValue = this.max;
                    }
                }
            }
        }
    }
};
</script>