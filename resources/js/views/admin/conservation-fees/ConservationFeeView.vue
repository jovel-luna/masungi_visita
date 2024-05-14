<template>
	<form-request :submit-url="submitUrl" @load="load" @success="fetch" confirm-dialog sync-on-success>
	
		<card>
			<template v-slot:header>Fees Information</template>

			<div class="row">
				<div class="form-group col-sm-12 col-md-6">
					<label>Name</label>
					<input v-model="item.name" name="name" type="text" class="form-control">
				</div>

				<selector class="col-sm-12 col-md-6"
				v-model="item.experience"
				name="experience_id"
				label="Experience"
				:items="experiences"
				item-value="id"
				item-text="name"
				required
				></selector>

				<selector class="col-sm-12 col-md-6"
				v-if="item.id"
				v-model="item.visitor_type"
				name="visitor_type_id"
				label="Visitor Type"
				:items="visitor_types"
				item-value="id"
				item-text="name"
				required
				></selector>

				<selector class="col-sm-12 col-md-6"
				v-if="item.id"
				v-model="item.special_fee"
				name="special_fee_id"
				label="Special Fee"
				:items="special_fees"
				item-value="id"
				item-text="name"
				></selector>

				<div class="form-group col-sm-12 col-md-6" v-if="item.id">
					<label>Weekday Fee</label>
					<input v-model="item.weekday_fee" name="weekday_fee" type="number" class="form-control">
				</div>

				<div class="form-group col-sm-12 col-md-6" v-if="item.id">
					<label>Weekend Fee</label>
					<input v-model="item.weekend_fee" name="weekend_fee" type="number" class="form-control">
				</div>

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
                :message="'Are you sure you want to archive ' + item.name + '?'"
                :alt-message="'Are you sure you want to restore ' + item.name + '?'"
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
import CrudMixin from '../../../mixins/crud.js';

import ActionButton from '../../../components/buttons/ActionButton.vue';
import Select from '../../../components/inputs/Select.vue';
import ImagePicker from '../../../components/inputs/ImagePicker.vue';
import TextEditor from '../../../components/inputs/TextEditor.vue';
import Datepicker from '../../../components/datepickers/Datepicker.vue';
import TimePicker from '../../../components/timepickers/Timepicker.vue';

export default {
	methods: {
		fetchSuccess(data) {
			this.item = data.item ? data.item : this.item;
			this.types = data.types ? data.types : this.types;
			this.experiences = data.experiences ? data.experiences : this.experiences;
			this.special_fees = data.special_fees ? data.special_fees : this.special_fees;
			this.visitor_types = data.visitor_types ? data.visitor_types : this.visitor_types;
		},
	},

	data() {
		return {
			item: [],
			types: [],
			experiences: [],
			special_fees: [],
			visitor_types: [],
		}
	},

	components: {
		'action-button': ActionButton,
		'selector': Select,
		'image-picker': ImagePicker,
		'text-editor': TextEditor,
		'date-picker': Datepicker,
		'time-picker': TimePicker,
	},

	mixins: [ CrudMixin ],
}
</script>