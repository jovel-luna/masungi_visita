<template>
	<form-request :submit-url="submitUrl" @load="load" @success="fetch" confirm-dialog sync-on-success>
	
		<card>
			<template v-slot:header>Training Modules Information</template>

			<div class="row">
				<div class="form-group col-sm-12 col-md-6">
					<label>Title</label>
					<input v-model="item.title" name="title" type="text" class="form-control">
				</div>

				<selector class="col-sm-12 col-md-6"
				v-model="item.destination_id"
				name="destination_id"
				label="Destination"
				:items="destinations"
				item-value="id"
				item-text="name"
				empty-text="None"
				placeholder="Please select a Destination"
				></selector>
				
			</div>

			


			<div class="row">
				<text-editor
				v-model="item.description"
				class="col-sm-12"
				label="Description"
				name="description"
				row="5"
				></text-editor>
			</div>
			<div class="row">

				<div class="form-group col-sm-12 col-md-12 mt-2">
					<div class="custom-control custom-switch">
						<input
						v-model="item.type"
						name="type" :checked="item.type" type="checkbox" class="custom-control-input" id="type">
						<label class="custom-control-label" for="type">Type is video?</label>
					</div>
				</div>
				
				<image-picker
				:value="item.path"
				class="form-group col-sm-12 col-md-12 mt-2"
                label="File"
                name="path"
                placeholder="Choose a File"
                :format="format"
				></image-picker>

			    <vue-player v-if="item.type === 1"
					:src="item.path"
					:video-placeholder-src="item.path"
					:poster="item.path"
					playsinline
				></vue-player>
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
                :message="'Are you sure you want to archive Destination #' + item.id + '?'"
                :alt-message="'Are you sure you want to restore Destination #' + item.id + '?'"
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
import vuePlayer  from  '@algoz098/vue-player'
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
			this.destinations = data.destinations ? data.destinations : this.destinations;
		},
	},

	data() {
		return {
			item: {
				type: false
			},
			destinations: [],
			format: '.jpeg, .jpg, .png'
		}
	},

	watch: {
		'item.type'(val) {
			if(val) {
				this.format = '.mp4, .ogg, .3gp, .wmv, .mov, .avi';
			} else {
				this.format = '.jpeg, .jpg, .png';
			}
		}
	},

	components: {
		'action-button': ActionButton,
		'selector': Select,
		'image-picker': ImagePicker,
		'text-editor': TextEditor,
		'date-picker': Datepicker,
		'time-picker': TimePicker,
		vuePlayer
	},

	mixins: [ CrudMixin ],
}
</script>