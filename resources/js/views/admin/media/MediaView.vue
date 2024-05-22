<template>
	<form-request :submit-url="submitUrl" @load="load" confirm-dialog sync-on-success>
	
		<card>
			<template v-slot:header>Media Information</template>

			<div class="row">
				<div class="form-group col-sm-12 col-md-12">
					<label>Media Title</label>
					<input v-model="name" name="title" type="text" class="form-control">
				</div>
			</div>

            <image-picker
            format="image/jpeg, image/png, image/gif"
            name="image_upload"
            @change="handleImageChange"
            ></image-picker>

			
		</card>

		<loader :loading="loading"></loader>
        <action-button type="submit" :disabled="loading" class="btn-primary">Save Changes</action-button>
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
		reInit() {
			axios.post(this.fetchUrl, data)
			.then(response => {
				// this.trails = response.data.trails;
                console.log(response)
			})
			.catch(error => {
				console.log(error);
			});
		},

        handleImageChange(event) {
            console.log(typeof(event.target.files[0])) // file type: will output object. this is file object
            console.log(event.target.files[0]) // file object
            console.log(event.target.files[0].name) // file name
            console.log(event.target.files[0].webkitRelativePath ) // path
        }
	},

	data() {
		return {
            name: null,
            file: null, 
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