<template>
	<div>
		<div class="rqst-frm1__step-2-content">
			<div class="align-l m-margin-b">
				<h5 class="frm-title small clr--gray">Main Contact Person</h5>
				<p class="clr--light-gray m-margin-t">{{ copywriting.description }}</p>
			</div>
			<hr>

			<div class="inlineBlock-parent align-l">
				<div class="width--50">
					<div class="width--95">
						<p class="frm-header bold s-margin-b clr--gray" :style=" { 'color' : errors.firstName }">First Name*</p>
						<div class="frm-inpt m-margin-b">
							<input type="text" v-model="stepData.main.first_name" @keypress="regexString();" @blur="isNull(stepData.main.first_name, 'firstName')">
						</div>
					</div>
				</div
				><div class="width--50">
					<div class="width--95 margin-l-a">
						<p class="frm-header bold s-margin-b clr--gray" :style=" { 'color' : errors.lastName }">Last Name*</p>
						<div class="frm-inpt m-margin-b">
							<input type="text" v-model="stepData.main.last_name" @keypress="regexString()" @blur="isNull(stepData.main.last_name, 'lastName')">
						</div>
					</div>
				</div>
			</div>

			<div class="inlineBlock-parent align-l">
				<div class="width--50">
					<div class="width--95">
						<p class="frm-header bold s-margin-b clr--gray" :style=" { 'color' : errors.nationality }">Nationality*</p>
						<div class="frm-inpt m-margin-b">
							<select v-model="stepData.main.nationality" @blur="isNull(stepData.main.nationality, 'nationality')">
								<option v-for="country in countries" :value="country.citizenship"> {{ country.citizenship }} </option>
							</select>
						</div>
					</div>
				</div
				><div class="width--50">
					<div class="width--95 margin-l-a">
						<p class="frm-header bold s-margin-b clr--gray" :style="{ 'color': color }">Email Address*</p>
						<div class="frm-inpt m-margin-b">
							<input type="email" v-model="stepData.main.email" @keyup="emailKeyPressed">
						</div>
					</div>
				</div>
			</div>

			<div class="inlineBlock-parent align-l">
				<div class="width--50 align-t">
					<div class="width--95">
						<p class="frm-header bold s-margin-b clr--gray"  :style=" { 'color' : errors.contactNumber }" @blur="isNull(stepData.main.contact_number, 'contactNumber')">Contact No.*</p>
						<div class="inlineBlock-parent">
							<!--< div class="width--30">
								<div class="width--90">
									<div class="frm-inpt align-c m-margin-b">
										<input type="text" name="" value="+63" disabled>
									</div>
								</div>
							</div
							> --><div class="width--100">
								<div class="frm-inpt align-c m-margin-b">
									<input type="text" name="" maxlength="15" placeholder="" v-model="stepData.main.contact_number" @keypress="regexNumber()" @blur="isNull(stepData.main.contact_number, 'contactNumber')">
									<p class="clr--light-gray"  :style=" { 'color' : errors.contactNumber }">{{ errors.contactNumber ? 'This field requires a minimum of 7 digits' : '' }}</p>
								</div>
							</div>
						</div>
					</div>
				</div
				><div class="width--50">
					<div class="width--95 margin-l-a">
						<p class="frm-header bold s-margin-b clr--gray"  :style=" { 'color' : errors.emergencyContactNumber }">Emergency Contact* </p>
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
									<input type="text" name="" placeholder="" maxlength="15" v-model="stepData.main.emergency_contact_number" @keypress="regexNumber()" @blur="isNull(stepData.main.emergency_contact_number, 'emergencyContactNumber')">
									<p class="clr--light-gray"  :style=" { 'color' : errors.emergencyContactNumber }">{{ errors.emergencyContactNumber ? 'This field requires a minimum of 7 digits' : '' }}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="inlineBlock-parent align-l">
				<div class="width--50">
					<div class="width--95">
						<p class="frm-header bold s-margin-b clr--gray"  :style=" { 'color' : errors.birthdate }">Birthdate*</p>
						<div class="frm-inpt m-margin-b">
							<input type="text" id="birthdate" v-model="stepData.main.birthdate" @blur="isNull(stepData.main.birthdate, 'birthdate')">
						</div>
					</div>
				</div
				><div class="width--50">
					<div class="width--95 margin-l-a">
						<p class="frm-header bold s-margin-b clr--gray" :style=" { 'color' : errors.gender }">Gender*</p>
						<div class="frm-inpt m-margin-b">
							<select v-model="stepData.main.gender" @blur="isNull(stepData.main.gender, 'gender')">
								<option v-for="gender in genders" :value="gender.name"> {{ gender.name }} </option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="inlineBlock-parent align-l">
				<div class="width--100">
					<div class="width--100">
						<p class="frm-header bold s-margin-b clr--gray"  :style=" { 'color' : errors.visitorType }">Visitor Type and Special Fee*</p>
						<div class="frm-inpt m-margin-b">
							<select v-model="stepData.main.visitor_type_id" @blur="isNull(stepData.main.visitor_type_id, 'visitorType')" @change="valueChanged">
								<option value="0">None</option>
								<option v-for="type in conservationFees" :value="type.id">{{ type.display_name }}</option>
							</select>
						</div>
					</div>
				</div
				><!-- <div class="width--50">
					<div class="width--95 margin-l-a">
						<p class="frm-header bold s-margin-b clr--gray">Special Fees</p>
						<div class="frm-inpt m-margin-b">
							<select v-model="stepData.main.special_fee_id">
								<option value="0">None</option>
								<option v-for="fee in specialFees" :value="fee.id"> {{ fee.name }} </option>
							</select>
						</div>
					</div>
				</div> -->
			</div>

			<div class="inlineBlock-parent align-l">
				<div class="width--50 align-t">
					<div class="width--95">
						<p class="frm-header bold s-margin-b clr--gray">Agency Code</p>
						<div class="frm-inpt m-margin-b">
							<input type="text" v-model="stepData.main.agency_code" @blur="checkIfAgencyCodeIsValid">
						</div>
					</div>
				</div
				><div class="width--50">
					<div class="width--95 margin-l-a" v-if="showFileUploader">
						<p class="frm-header bold s-margin-b clr--gray">Valid ID / Identification document*</p>
						<div class="frm-inpt m-margin-b">
							<input type="file" id="files" @change="proofForSpecialFee" accept="image/*, .pdf">
							<p class="clr--light-gray .frm-lbl__smll">Only upload .PDF and .JPG files</p>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="inlineBlock-parent width--100 align-l">
			<p class="frm-header bold clr--green">NOTE:</p>
			<p class="frm-header clr--gray">Contact Person should be 18 years old and above.</p>
		</div>
		<loading :active.sync="isLoading" :is-full-page="true"></loading>
	</div>
</template>

<script>
	/* Flatpickr Documentation: https://flatpickr.js.org/options/ */
	import flatpickr from 'flatpickr';
	import 'flatpickr/dist/flatpickr.css';
	import RegexMixin from 'Mixins/regex.js';
	import ErrorResponseHandler from 'Mixins/errorResponse.js';
	import {EventBus} from 'Root/EventBus.js';

	export default {
		props: {
			visitorTypes: Array,
			specialFees: Array,
			stepData: Object,
			countries: Array,
			genders: Array,
			destination: Object,
			agencyCodeUrlChecker: String,
			copywriting: Object
		},

		data() {
			return {
				color: null,
				reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
				errors: {
					firstName: null,
					lastName: null,
					contactNumber: null,
					emergencyContactNumber: null,
					gender: null,
					nationality: null,
					visitorType: null,
					birthdate: null,
				},
				isLoading: false,
				showFileUploader: false
			}
		},

		mixins: [ RegexMixin, ErrorResponseHandler ],

		components: {
			Loading,
		},

		computed: {
			showFileInput() {
				if(this.stepData.main.visitor_type_id != 0) { return true }
				return false;
			},

			firstNameNull() {
				if(this.stepData.main.first_name == null) return '#d43f3f';

				return null;
			},

			lastNameNull() {
				if(this.stepData.main.last_name == null) return '#d43f3f';

				return null;
			},

			contactNumberNull() {
				if(this.stepData.main.contact_number == null) return '#d43f3f';

				return null;
			},

			emergencyContactNumberNull() {
				if(this.stepData.main.emergency_contact_number == null) return '#d43f3f';

				return null;
			},

			conservationFees() {
				var conservationFees = this.destination.conservationFees;
				var arr = [];
				_.each(conservationFees, (fee) => {
					if(fee.experience_id === this.stepData.allocationSelected) {
						arr.push(fee);
					}
				});

				return arr;
				// return conservationFees;
			}
		},

		watch: {
			'stepData.main.paths'(val) {
				if($('#files')[0].files.length) {
					EventBus.$emit('hasFile', true);
				} else {
					EventBus.$emit('hasFile', false);
				}
			}
		},

		mounted() {
			var validDate = moment().add(1,'days').subtract(18,'years').format('YYYY-MM-DD');
			var todayAndFutureDate = moment().add(1000,'years').format('YYYY-MM-DD');
			// flatpickr('#birthdate', { maxDate: new Date().fp_incr(-6570), disableMobile: 'true' });
			flatpickr('#birthdate', { maxDate: new Date().fp_incr(-6570), disable: [
					{
						from: validDate,
						to: todayAndFutureDate
					}
				], disableMobile: 'true' });

			this.valueChanged();
		},

		methods: {

			valueChanged(){
				var conservationFees = this.destination.conservationFees;
				_.each(conservationFees, (fee) => {
					if(fee.id === this.stepData.main.visitor_type_id) {
						if(!fee.special_fee_id) {
							this.showFileUploader = false;
							EventBus.$emit('showFileUploader', false);
						} else {
							this.showFileUploader = true;
							EventBus.$emit('showFileUploader', true);
						}
					}
				});
			},

			checkIfAgencyCodeIsValid() {
				if(this.stepData.main.agency_code) {
					this.isLoading = true;
					var payload = {
						code : this.stepData.main.agency_code,
						destination: this.destination.id,
						allocation: this.stepData.allocationSelected,
						date: this.stepData.visitDate
					}

					axios.post(this.agencyCodeUrlChecker, payload)
						.then(response => {
							this.isLoading = false;
							this.destination.agencyAvailableSeat = response.data.remaining;
						}).catch(errors => {
							this.isLoading = false;
							this.stepData.main.agency_code = null;
							swal.fire('Oops...', this.parseResponse(errors, 'error'), 'error')
						})
					} else {
						this.destination.agencyAvailableSeat = this.destination.availableSeat;
					}
			},

			proofForSpecialFee(e) {
	            var files = e.target.files || e.dataTransfer.files;

	            if(!files.length) {
            		this.stepData.main.paths = [];
            	    return;
	            }

	            this.stepData.main.paths = files[0];
	        },

	        emailKeyPressed() {
	        	if(!this.reg.test(this.stepData.main.email)) {
	        		this.color = '#d43f3f';
	        	} else {
	        		this.color = null;
	        	}
	        },

	        isNull(model, error) {
	        	if(model == null || model == '') {
	        		if(error === 'firstName') {
	        			this.errors.firstName = '#d43f3f';
	        		}

	        		if(error === 'lastName') {
	        			this.errors.lastName = '#d43f3f';
	        		}

	        		if(error === 'nationality') {
	        			this.errors.nationality = '#d43f3f';
	        		}

	        		if(error === 'contactNumber') {
	        			this.errors.contactNumber = '#d43f3f';
	        		}

	        		if(error === 'emergencyContactNumber') {
	        			this.errors.emergencyContactNumber = '#d43f3f';
	        		}

	        		if(error === 'birthdate') {
	        			this.errors.birthdate = '#d43f3f';
	        		}

	        		if(error === 'gender') {
	        			this.errors.gender = '#d43f3f';
	        		}

					if(error === 'visitorType') {
	        			this.errors.visitorType = '#d43f3f';
	        		}
	        	} else {
	        		if(error === 'firstName') {
	        			this.errors.firstName = null;
	        		}

	        		if(error === 'lastName') {
	        			this.errors.lastName = null;
	        		}

	        		if(error === 'nationality') {
	        			this.errors.nationality = null;
	        		}

	        		if(error === 'contactNumber') {
	        			this.errors.contactNumber = null;
	        			if(model.length < 7) {
	        				this.errors.contactNumber = '#d43f3f';
	        			}
	        		}

	        		if(error === 'emergencyContactNumber') {
	        			this.errors.emergencyContactNumber = null;
	        			if(model.length < 7) {
	        				this.errors.emergencyContactNumber = '#d43f3f';
	        			}
	        		}

	        		if(error === 'birthdate') {
	        			this.errors.birthdate = null;
	        		}

	        		if(error === 'gender') {
	        			this.errors.gender = null;
	        		}

					if(error === 'visitorType') {
	        			this.errors.visitorType = null;
	        		}
	        	}
	        }
		}
	}
</script>