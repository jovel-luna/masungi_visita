<template>
	<form-request :submit-url="submitUrl" @load="load" @success="fetch" confirm-dialog sync-on-success>

		<card>
			<template v-slot:header>Transaction Fee Details</template>

			<div class="row">
				<div class="form-group col-sm-6 col-md-6">
					<label>Name</label>
					<input v-model="item.name" name="name" type="text" class="form-control">
				</div>
				<div class="form-group col-sm-6 col-md-6">
					<label>Paynamics Payment Method Code</label>
					<input v-model="item.code" name="code" type="text" class="form-control">
				</div>

				<selector class="col-sm-6 col-md-6"
				v-model="item.type"
				name="type"
				label="Type"
				:items="types"
				item-value="value"
				item-text="label"
				placeholder="Select type"
				></selector>

			</div>

			<div class="row">

				<div class="form-group col-sm-6 col-md-6" v-if="item.type != 'PERCENTAGE'">
					<label>Fixed Amount</label>
					<input v-model="item.fixed_amount" name="fixed_amount" type="number" min="1" step="0.01" class="form-control">
				</div>
                <number-field
                    v-if="item.type != 'FIXED'"
                    v-model="item.percentage_amount"
                    class="col-6"
                    type="integer"
                    label="Percentage Amount"
                    name="percentage_amount"
                />
			</div>

			<div class="row">

				<image-picker
				:value="item.path"
				class="form-group col-sm-12 col-md-12 mt-2"
                label="Image"
                name="image_path"
                placeholder="Choose a File"
				></image-picker>

			</div>



			<template v-slot:footer>
                <action-button
                v-if="item.archiveUrl && item.restoreUrl"
                color="btn-danger"
                alt-color="btn-warning"
                :action-url="item.archiveUrl"
                :alt-action-url="item.restoreUrl"
                label="Archive"
                alt-label="Restore"
                :show-alt="item.deleted_at"
                confirm-dialog
                title="Archive Item"
                alt-title="Restore Item"
                :message="'Are you sure you want to archive Transaction Fee #' + item.id + '?'"
                :alt-message="'Are you sure you want to restore Transaction Fee #' + item.id + '?'"
                :disabled="loading"
                @load="load"
                @success="fetch"
                @error="fetch"
                ></action-button>

				<action-button type="submit" :disabled="loading" class="btn-primary">Save Changes</action-button>
			</template>
		</card>

		<loader :loading="loading"></loader>

	</form-request>
</template>

<script type="text/javascript">
import { EventBus }from '../../../EventBus.js';
import CrudMixin from 'Mixins/crud.js';

import ActionButton from 'Components/buttons/ActionButton.vue';
import Select from 'Components/inputs/Select.vue';
import ImagePicker from 'Components/inputs/ImagePicker.vue';
import TextEditor from 'Components/inputs/TextEditor.vue';
import NumberField from 'Components/inputs/NumberField.vue';
import Datepicker from 'Components/datepickers/Datepicker.vue';
import TimePicker from 'Components/timepickers/Timepicker.vue';

export default {
	methods: {
		fetchSuccess(data) {
			this.item = data.item ? data.item : this.item;

            if (this.item.percentage_amount) {
                this.item.percentage_amount = Math.floor(this.item.percentage_amount);
            } else {
                this.item.percentage_amount = 0;
            }
			this.types = data.types ? data.types : this.types;
		},
	},

	data() {
		return {
			item: {
                percentage_amount: 0
            },
			types: [],
		}
	},

	components: {
		'action-button': ActionButton,
		'selector': Select,
		'image-picker': ImagePicker,
		'text-editor': TextEditor,
		'date-picker': Datepicker,
		'time-picker': TimePicker,
        'number-field': NumberField
	},

	mixins: [ CrudMixin ],
}
</script>