<template>
	<div>
		<div class="row"  >
			<div class="form-group col-sm-12 col-md-12">
				<h3>
					<b>Guest #{{ index }}</b>

					<button v-if="!guest.archiveUrl && bookingDetails.from_masungi_reservation && !bookingDetails.is_invoice_approved" type="button" class="btn btn-sm btn-danger" @click="$emit('removeGuest')">Remove</button>
					<!-- Uncomment if guest removal will be requested -->
					<!-- <action-button
					v-if="guest.archiveUrl && bookingDetails.from_masungi_reservation && !bookingDetails.is_invoice_approved"
					color="btn-sm btn-danger"
					alt-color="btn-warning"
					:action-url="guest.archiveUrl"
					label="Archive"
					confirm-dialog
					title="Archive Item"
					:message="'Are you sure you want to archive Guest #' + guest.id + '?'"
					@load="$emit('load')"
					@success="$emit('fetch')"
					></action-button> -->
				</h3>
			</div>
			<div class="form-group col-sm-12 col-md-4">
				<label>Firstname</label>
				<input v-model="guest.first_name" :name="'guest_first_name['+(index-1)+']'" type="text" class="form-control">
				<input v-model="guest.id" :name="'guest_id['+(index-1)+']'" type="text" class="form-control" v-show="hide">
			</div>
			<div class="form-group col-sm-12 col-md-4">
				<label>Lastname</label>
				<input v-model="guest.last_name" :name="'guest_last_name['+(index-1)+']'" type="text" class="form-control">
			</div>
			<selector class="col-sm-4"
			v-model="guest.nationality"
			:name="'guest_nationality['+(index-1)+']'"
			label="Nationality"
			:items="nationalities"
			item-value="citizenship"
			item-text="citizenship"
			placeholder="Select Nationality"
			></selector>
			<div class="form-group col-sm-12 col-md-4">
				<label>Email</label>
				<input v-model="guest.email" :name="'guest_email['+(index-1)+']'" type="email" class="form-control">
			</div>

			<div class="form-group col-sm-12 col-md-4">
				<label>Contact #</label>
				<input v-model="guest.contact_number" :name="'guest_contact_number['+(index-1)+']'" @keypress="mobileNumber()" class="form-control">
			</div>
			<div class="form-group col-sm-12 col-md-4">
				<label>Emergency Contact #</label>
				<input v-model="guest.emergency_contact_number" :name="'guest_emergency_contact_number['+(index-1)+']'" @keypress="mobileNumber()" class="form-control">
			</div>

			<div class="form-group col-sm-12 col-md-4">
				<label>Birthday</label>
				<input :name="'guest_birthdate['+(index-1)+']'" v-model="guest.birthdate" type="text" :id="'birthdate-flatpickr'+guest.id" class="form-control">
			</div>

<!-- 			<date-picker
			v-model="guest.birthdate"

			label="Birthday"
			:enableTime="false"
			name="guest_birthdate[]"
			placeholder="Choose a Birthday"
			date-format="Y-m-d"
			></date-picker> -->

			<selector class="col-sm-4"
			v-model="guest.gender"
			:name="'guest_gender['+(index-1)+']'"
			label="Gender"
			:items="genders"
			item-value="name"
			item-text="name"
			placeholder="Select Gender"
			></selector>

			<selector class="col-sm-4"
			v-model="guest.conservation_fee_id"
			:name="'guest_conservation_fee_id['+(index-1)+']'"
			label="Visitor Type & Special Fee"
			:items="fees"
			item-value="id"
			item-text="display_name"
			placeholder="Select Type of Visitor & Special Fee"
			></selector>

<!-- 			<selector class="col-sm-4"
			v-model="guest.special_fee_id"
			:name="'guest_special_fee_id['+(index-1)+']'"
			label="Special Fees"
			:items="specialFees"
			item-value="id"
			item-text="name"
			placeholder="Select Special Fee"
			></selector>

			<selector class="col-sm-4"
			v-model="guest.visitor_type_id"
			:name="'guest_visitor_type['+(index-1)+']'"
			label="Visitor Type"
			:items="visitorTypes"
			item-value="id"
			item-text="name"
			placeholder="Select Type of Visitor"
			></selector> -->

			<image-picker
			:value="guest.specialFeeImagePath"
			class="form-group col-sm-12 col-md-12"
            label="Image"
            :name="'guest_special_fee_path['+(index-1)+']'"
            placeholder="Choose a File"
			></image-picker>
		</div>
	</div>
</template>
<script>
import Datepicker from '../../../components/datepickers/Datepicker.vue';
import Select from '../../../components/inputs/Select.vue';
import ImagePicker from '../../../components/inputs/ImagePicker.vue';
import ActionButton from '../../../components/buttons/ActionButton.vue';
import RegexMixin from 'Mixins/regex.js';

export default {
	mixins: [ RegexMixin ],

	props: {
		guest: Object,
		nationalities: Array,
		index: Number,
		genders: Array,
		specialFees: Array,
		visitorTypes: Array,
		fees: Array,
		bookingDetails: Object,
	},
	mounted() {
		// $('#birthdate-flatpickr').flatpickr();
		flatpickr('#birthdate-flatpickr'+this.guest.id, { maxDate: 'today', disableMobile: 'true' });
	},

	data() {
		return {
			date: null,
			hide: false
		}
	},

	components: {
		'selector': Select,
		'date-picker': Datepicker,
		'image-picker': ImagePicker,
		'action-button': ActionButton,
	},
}
</script>