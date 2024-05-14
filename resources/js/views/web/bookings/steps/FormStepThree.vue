<template>
	<div>
		<div class="inlineBlock-parent">
			<div class="width--60">
				<h5 class="frm-title small clr--gray">Guest {{ headCount }} Details</h5>
			</div
			><div class="width--40 align-r inlineBlock-parent">
				<input type="checkbox" class="s-margin-r" @change="sameAsContactPerson()" v-model="isSameDetails">
				<p class="frm-header clr--gray mb-0">Same as contact person</p>
			</div>
		</div>
		<hr>
		<div class="inlineBlock-parent align-l">
			<div class="width--50">
				<div class="width--95">
					<p class="frm-header bold s-margin-b clr--gray">First Name*</p>
					<div class="frm-inpt m-margin-b">
						<input type="text" v-model="guest.first_name" @keypress="regexString($evt);" >
					</div>
				</div>
			</div
			><div class="width--50">
				<div class="width--95 margin-l-a">
					<p class="frm-header bold s-margin-b clr--gray">Last Name*</p>
					<div class="frm-inpt m-margin-b">
						<input type="text" v-model="guest.last_name" @keypress="regexString($evt)" >
					</div>
				</div>
			</div>
		</div>

		<div class="inlineBlock-parent align-l">
			<div class="width--50">
				<div class="width--95">
					<p class="frm-header bold s-margin-b clr--gray">Nationality*</p>
					<div class="frm-inpt m-margin-b">
						<select v-model="guest.nationality" >
							<option v-for="country in countries" :value="country.citizenship">{{ country.citizenship }}</option>
						</select>
					</div>
				</div>
			</div
			><div class="width--50">
				<div class="width--95 margin-l-a">
					<p class="frm-header bold s-margin-b clr--gray">Email Address*</p>
					<div class="frm-inpt m-margin-b">
						<input type="email" v-model="guest.email">
					</div>
				</div>
			</div>
		</div>

		<div class="inlineBlock-parent align-l">
			<div class="width--50">
				<div class="width--95">
					<p class="frm-header bold s-margin-b clr--gray">Contact No.*</p>
					<div class="inlineBlock-parent">
						<!-- <div class="width--30">
							<div class="width--90">
								<div class="frm-inpt align-c m-margin-b">
									<input type="text" name="" value="+63" disabled>
								</div>
							</div>
						</div
						> --><div class="width--100">
							<div class="frm-inpt align-c m-margin-b">
								<input type="text" name="" placeholder="" maxlength="15" v-model="guest.contact_number" @keypress="regexNumber($evt)" >
							</div>
						</div>
					</div>
				</div>
			</div
			><div class="width--50">
				<div class="width--95 margin-l-a">
					<p class="frm-header bold s-margin-b clr--gray">Emergency Contact*</p>
					<div class="inlineBlock-parent">
						<!-- <div class="width--30">
							<div class="width--90">
								<div class="frm-inpt align-c m-margin-b">
									<input type="text" name="" value="+63" disabled>
								</div>
							</div>
						</div
						> --><div class="width--100">
							<div class="frm-inpt align-c m-margin-b">
								<input type="text" name="" placeholder="" maxlength="15" v-model="guest.emergency_contact_number" @keypress="regexNumber($evt)" >
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="inlineBlock-parent align-l">
			<div class="width--50">
				<div class="width--95">
					<p class="frm-header bold s-margin-b clr--gray">Birthdate*</p>
					<div class="frm-inpt m-margin-b">
						<input type="text" id="birthdate-guests" v-model="guest.birthdate" >
					</div>
				</div>
			</div
			><div class="width--50">
				<div class="width--95 margin-l-a">
					<p class="frm-header bold s-margin-b clr--gray">Gender*</p>
					<div class="frm-inpt m-margin-b">
						<select v-model="guest.gender" >
							<option v-for="gender in genders" :value="gender.name"> {{ gender.name }} </option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="inlineBlock-parent align-l">
			<div class="width--100">
				<div class="width--100">
					<p class="frm-header bold s-margin-b clr--gray">Visitor Type and Special Fee*</p>
					<div class="frm-inpt m-margin-b">
						<select v-model="guest.visitor_type_id" @change="valueChanged">
							<option v-for="type in conservationFees" :value="type.id"> {{ type.display_name }} </option>
							<!-- <option v-for="type in visitorTypes" :value="type.id"> {{ type.name }} </option> -->
						</select>
					</div>
				</div>
			</div
			><!-- <div class="width--50">
				<div class="width--95 margin-l-a">
					<p class="frm-header bold s-margin-b clr--gray">Special Fees</p>
					<div class="frm-inpt m-margin-b">
						<select v-model="guest.special_fee_id">
							<option value="0">None</option>
							<option v-for="fee in specialFees" :value="fee.id">{{ fee.name }}</option>
						</select>
					</div>
				</div>
			</div> -->
		</div>

		<div class="inlineBlock-parent align-l" v-if="showFileUploader">
			<div class="width--50">
				<div class="width--95">
					<p class="frm-header bold s-margin-b clr--gray">Valid ID / Identification document *</p>
					<div class="frm-inpt m-margin-b">
						<input type="file" @change="proofForSpecialFee" v-if="!hasFileAttached" accept="image/*, .pdf">
						<div class="align-l" v-if="hasFileAttached">
							<button class="frm-btn green" @click="hasFileAttached = false">Already attached a file, click here to update</button>
							<p class="clr--light-gray .frm-lbl__smll">Only upload .PDF and .JPG files</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="align-l">
			<button class="frm-btn green" @click="$emit('submitGuestDetails', guestKey, guest)" v-if="showSubmit">Submit</button>
		</div>
	</div>
</template>

<script>
	/* Flatpickr Documentation: https://flatpickr.js.org/options/ */
	import flatpickr from 'flatpickr';
	import 'flatpickr/dist/flatpickr.css';
	import RegexMixin from 'Mixins/regex.js';

	export default{
		props: {
			guestKey: Number,
			stepData: Object,
			countries: Array,
			genders: Array,
			visitorTypes: Array,
			specialFees: Array,
			main: Object
		},

		mixins: [ RegexMixin ],

		data() {
			return {
				reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
				guest: {
					paths: []
				},
				errors: {},
				color: null,
				headCount: this.guestKey + 1,
				isSameDetails: false,
				hasFile: false,
				hasFileAttached: false,
				showFileUploader: true,
			}
		},

		computed: {
			/*showFileInput() {
				if(this.hasFile == false) { return false }
				return true;
			},*/

			showSubmit() {
				if(this.guest.first_name != '' && this.guest.gender != null &&
					this.guest.nationality != '' && this.guest.last_name != '' &&
					this.guest.email != '' && this.guest.birthdate != null &&
					this.guest.contact_number != '' && this.guest.emergency_contact_number != '' &&
					this.guest.visitor_type_id != 0 && this.reg.test(this.guest.email) &&
					this.guest.contact_number.length >= 7 && this.guest.contact_number.length <= 15  &&
					this.guest.emergency_contact_number.length >= 7  && this.guest.emergency_contact_number.length <= 15) {
/*					if(this.hasFile == false) {
						return this.hasFile;
					}*/
					if(this.hasFile || !this.showFileUploader) {
						return true
					} else {
						return false
					}
					return true;
				}

				return false;
			},

			conservationFees() {
				var conservationFees = this.$parent.destination.conservationFees;
				var arr = [];
				_.each(conservationFees, (fee) => {
					if(fee.experience_id === this.$parent.stepData.allocationSelected) {
						arr.push(fee);
					}
				});

				return arr;
			}
		},

		watch: {
			'guest.paths'(val) {
				if(typeof(val) == 'object') {
					if(val.name != undefined) {
						this.hasFile = true
						this.hasFileAttached = true;
					} else {
						this.hasFile = false
					}
				} else {
					this.hasFile = false
				}
				console.log(val, typeof(val) == 'object', typeof(val));
			},

			isSameDetails(val) {
				if(val) {
					this.guest = {
						special_fee_id: this.main.special_fee_id,
						visitor_type_id: this.main.visitor_type_id,
						first_name: this.main.first_name,
						gender: this.main.gender,
						nationality: this.main.nationality,
						last_name: this.main.last_name,
						email: this.main.email,
						birthdate: this.main.birthdate,
						contact_number: this.main.contact_number,
						emergency_contact_number: this.main.emergency_contact_number,
						main: false,
						paths: this.main.paths
					}
				} else {
					this.guest = {
						paths: []
					}
				}
			}
		},

		mounted() {
			flatpickr('#birthdate-guests', { maxDate: 'today', disableMobile: 'true' });
			this.setupGuestData();
		},

		methods: {
			valueChanged(){
				var conservationFees = this.$parent.destination.conservationFees;
				_.each(conservationFees, (fee) => {
					if(fee.id === this.guest.visitor_type_id) {
						if(!fee.special_fee_id) {
							this.showFileUploader = false;
							console.log('false');
						} else {
							this.showFileUploader = true;
							console.log('true');
						}
					}
				});
			},

			setupGuestData() {
				if(this.stepData.first_name != null) {
					this.guest = this.stepData;
				} else {
					this.guest = {
						special_fee_id: 0,
						visitor_type_id: 0,
						first_name: null,
						gender: null,
						nationality: null,
						last_name: null,
						email: null,
						birthdate: null,
						contact_number: null,
						emergency_contact_number: null,
						main: false,
						paths: [],
					}
				}

			},

			proofForSpecialFee(e) {
            	this.guest.paths = [];
	            var files = e.target.files || e.dataTransfer.files;

	            if(!files.length){
	            	this.guest.paths = [];
	                return;
	            }

	            this.guest.paths = files[0];
	        },


	        sameAsContactPerson() {
	        	// this.isSameDetails = true;
	        	this.guest = {
						special_fee_id: this.main.special_fee_id,
						visitor_type_id: this.main.visitor_type_id,
						first_name: this.main.first_name,
						gender: this.main.gender,
						nationality: this.main.nationality,
						last_name: this.main.last_name,
						email: this.main.email,
						birthdate: this.main.birthdate,
						contact_number: this.main.contact_number,
						emergency_contact_number: this.main.emergency_contact_number,
						main: false,
						paths: this.main.paths
					}
	        },
		}
	}
</script>