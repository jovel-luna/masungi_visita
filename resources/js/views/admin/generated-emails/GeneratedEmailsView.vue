<template>
	<form-request :submit-url="submitUrl" @load="load" @success="fetch;reInit()" confirm-dialog sync-on-success>
	
		<card>
			<template v-slot:header>Generated Email Information</template>

			<div class="row">
				<selector class="col-sm-12 col-md-12"
				v-model="item.notification_type"
				name="notification_type"
				label="Type"
				@change="reInit()"
				:items="types"
				item-value="value"
				item-text="label"
				placeholder="Please select a type"
				></selector>
			</div>

			<div class="row">
				<selector class="col-sm-12 col-md-12"
				v-model="item.email_to"
				name="email_to"
				label="Email To"
				:items="email_types"
				item-value="value"
				item-text="label"
				placeholder="Please select a type"
				></selector>
			</div>

			<div v-if="item.email_to == 'MASUNGI'" class="row">
				<selector class="col-sm-12 col-md-12"
				v-model="item.experience_id"
				name="experience_id"
				label="Trails"
				:items="trails"
				item-value="id"
				item-text="name"
				placeholder="Please select a type"
				></selector>
			</div>

			<div class="row">
				<div class="form-group col-sm-12 col-md-12">
					<label>Title</label>
					<input v-model="item.title" name="title" type="text" class="form-control">
				</div>
			</div>

			<div class="row">
				<text-editor
				v-model="item.message"
				class="col-sm-12"
				label="Message"
				name="message"
				row="5"
				></text-editor>
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
                :message="'Are you sure you want to archive Generated Email #' + item.id + '?'"
                :alt-message="'Are you sure you want to restore Generated Email #' + item.id + '?'"
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
			this.email_types = data.email_types ? data.email_types : this.email_types;
			this.trails = data.trails ? data.trails : this.trails;
			this.reInit();
		},

		reInit() {
			let data = {
				id: this.item.id,
				notification_type: this.item.notification_type,
			};

			axios.post(this.fetchUrl, data)
			.then(response => {
				this.trails = response.data.trails;
			})
			.catch(error => {
				console.log(error);
			});
		},
	},

	data() {
		return {
			item: [],
			types: [],
			email_types: [],
			trails: [],
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