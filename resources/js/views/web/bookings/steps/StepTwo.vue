<template>
	<div>
		<div class="width--90 margin-a rqst-frm1__steps-form-cards-container gnrl-scrll">
			<div class="align-l">
				<h5 class="frm-title x-small clr--gray">Experience & Schedule</h5>
				<hr>
				<div class="inlineBlock-parent">
					<p class="frm-header bold clr--gray">Date:</p>
					<p class="frm-header clr--gray">{{ toDate(stepData.visitDate) }}</p>
				</div>
				<div class="inlineBlock-parent">
					<p class="frm-header bold clr--gray">Experience:</p>
					<p class="frm-header clr--gray">{{ allocation.allocation_name }}</p>
				</div>
				<div class="inlineBlock-parent">
					<p class="frm-header bold clr--gray">No. of guests:</p>
					<p class="frm-header clr--gray">{{ stepData.numberOfGuests }}</p>
				</div>
				<div class="inlineBlock-parent">
					<p class="frm-header bold clr--gray">Time:</p>
					<p class="frm-header clr--gray">{{ toTime(stepData.timeSelected, 'hh:mm A') }}</p>
				</div>
			</div>
		</div>
		<hr>
		<div class="inlineBlock-parent">
			<div class="width--45">
				<div class="width--95">
					<button
					  	class="frm-btn gray"
					  	@click="$emit('returnStep1')"
					>Back</button>
				</div>
			</div
			><div class="width--45">
				<div class="width--95">
					<button 
						v-show="detailsComplete"
					  	class="frm-btn green"
					  	@click="$emit('showStep3')"
					>Next</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import DateMixin from 'Mixins/date.js';
	import {EventBus} from 'Root/EventBus.js';
	
	export default {
		props: {
			stepData: Object,
			items: Array,
			allocation: Object
		},

		data() {
			return {
				reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
				hasFile: false,
				showFileUploader: false,
				checker: {
					first_name: false,
					last_name: false,
					nationality: false,
					gender: false,
					email: false,
					birthdate: false,
					contact_number: false,
					emergency_contact_number: false,
					visitor_type_id: false,
				},
			}
		},

		mixins: [ DateMixin ],

		computed: {
			/*detailsComplete() {
				if(this.stepData.main.first_name != '' && this.stepData.main.first_name && this.stepData.main.gender != '' && 
					this.stepData.main.nationality != '' && this.stepData.main.last_name != '' && this.stepData.main.last_name &&
					this.stepData.main.email != '' && this.stepData.main.birthdate != '' && 
					this.stepData.main.contact_number != '' && this.stepData.main.emergency_contact_number != '' &&
					this.stepData.main.visitor_type_id != 0 && this.reg.test(this.stepData.main.email) && 
					this.stepData.main.emergency_contact_number.length >= 7 && this.stepData.main.emergency_contact_number.length <= 15 && 
					this.stepData.main.contact_number.length >= 7 && this.stepData.main.contact_number.length <= 15) {

					// if(this.stepData.main.special_fee_id > 0 && _.isEmpty(this.stepData.main.paths)) {
					// 	return false;
					// }
					// if(this.stepData.main.special_fee_id != 0) {
						if(this.hasFile || !this.showFileUploader) {
							return true
						} else {
							return false
						}
					// }
					return true;
				}

				return false;
			},*/

			detailsComplete() {
				if(this.checker.first_name && this.checker.last_name && this.checker.nationality && this.checker.gender && this.checker.birthdate && this.checker.email && this.checker.visitor_type_id && this.checker.contact_number && this.checker.emergency_contact_number) {
					if(this.hasFile || !this.showFileUploader) {
							return true;
						} else {
							return false;
					}
					return true;
				}
				return false;
			}
		},

		watch: {
			'stepData.main.first_name'(val) {
				if(val != '' && val) {
					this.checker.first_name = true;
				} else {
					this.checker.first_name = false;
				}
			},

			'stepData.main.last_name'(val) {
				if(val != '' && val) {
					this.checker.last_name = true;
				} else {
					this.checker.last_name = false;
				}
			},

			'stepData.main.gender'(val) {
				if(val != null) {
					this.checker.gender = true;
				} else {
					this.checker.gender = false;
				}
			},

			'stepData.main.nationality'(val) {
				if(val != '') {
					this.checker.nationality = true;
				} else {
					this.checker.nationality = false;
				}
			},

			'stepData.main.visitor_type_id'(val) {
				if(val != '') {
					this.checker.visitor_type_id = true;
				} else {
					this.checker.visitor_type_id = false;
				}
			},

			'stepData.main.birthdate'(val) {
				if(val != null) {
					this.checker.birthdate = true;
				} else {
					this.checker.birthdate = false;
				}
			},

			'stepData.main.email'(val) {
				if(val != '' && this.reg.test(val)) {
					this.checker.email = true;
				} else {
					this.checker.email = false;
				}
			},

			'stepData.main.contact_number'(val) {
				if(val != '' && val.length >= 7 && val.length <= 15) {
					this.checker.contact_number = true;
				} else {
					this.checker.contact_number = false;
				}
			},

			'stepData.main.emergency_contact_number'(val) {
				if(val != '' && val.length >= 7 && val.length <= 15) {
					this.checker.emergency_contact_number = true;
				} else {
					this.checker.emergency_contact_number = false;
				}
			},

		},

		created() {
			EventBus.$on('hasFile', (data) => {
				this.hasFile = data;
			});

			EventBus.$on('showFileUploader', (data) => {
				this.showFileUploader = data;
			});
		},

		mounted() {
			this.checkDetails();
		},

		methods: {
			// isEmailValid() {
		 //      	return (this.stepData.main.email == "")? "" : (this.reg.test(this.stepData.main.email)) ? 'has-success' : 'has-error';
		 //    }

		 	checkDetails() {
				if(this.stepData.main.first_name != '' && this.stepData.main.first_name) {
					this.checker.first_name = true;
				} else {
					this.checker.first_name = false;
				}

				if(this.stepData.main.last_name != '' && this.stepData.main.last_name) {
					this.checker.last_name = true;
				} else {
					this.checker.last_name = false;
				}

				if(this.stepData.main.gender != null) {
					this.checker.gender = true;
				} else {
					this.checker.gender = false;
				}

				if(this.stepData.main.nationality != '') {
					this.checker.nationality = true;
				} else {
					this.checker.nationality = false;
				}

				if(this.stepData.main.visitor_type_id != '') {
					this.checker.visitor_type_id = true;
				} else {
					this.checker.visitor_type_id = false;
				}

				if(this.stepData.main.birthdate != null) {
					this.checker.birthdate = true;
				} else {
					this.checker.birthdate = false;
				}

				if(this.stepData.main.email != '' && this.reg.test(this.stepData.main.email)) {
					this.checker.email = true;
				} else {
					this.checker.email = false;
				}

				if(this.stepData.main.contact_number != '' && this.stepData.main.contact_number.length >= 7 && this.stepData.main.contact_number.length <= 15) {
					this.checker.contact_number = true;
				} else {
					this.checker.contact_number = false;
				}

				if(this.stepData.main.emergency_contact_number != '' && this.stepData.main.emergency_contact_number.length >= 7 && this.stepData.main.emergency_contact_number.length <= 15) {
					this.checker.emergency_contact_number = true;
				} else {
					this.checker.emergency_contact_number = false;
				}
		 	}
		}
	}
</script>