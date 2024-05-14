<template>
	<form-request :submit-url="submitUrl" @load="load" @success="fetch" confirm-dialog sync-on-success >
	
		<card>
			<template v-slot:header>Capacity Information</template>
			<div class="row">
				<selector class="col-sm-4"
				v-model="item.allocation_id"
				name="allocation_id"
				label="Experience"
				:items="allocations"
				item-value="id"
				item-text="name"
				placeholder="Select Experience"
				@change="allocationChanged()"
				></selector>
				<div class="form-group col-sm-12 col-md-4">
					<label>Total Capacity</label>
					<input v-model="total_destination_capacity" type="number" min="1" class="form-control" disabled>
				</div>
				<div class="form-group col-sm-12 col-md-4">
					<label>Remaining Capacity</label>
					<input :value="remainingCapacity" type="number" min="1" class="form-control" disabled>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-sm-12 col-md-6">
					<label>Online</label>
					<input v-model="item.online" name="online" type="number" max="" min="0" class="form-control">
				</div>
				<div class="form-group col-sm-12 col-md-6">
					<label>Walk In</label>
					<input v-model="item.walk_in" name="walk_in" type="number" min="0" class="form-control">
				</div>
				<div class="form-group col-sm-12 col-md-6">
					<label>Management (LGU)</label>
					<input v-model="item.mgt_lgu" name="mgt_lgu" type="number" min="0" class="form-control">
				</div>
				<div class="form-group col-sm-12 col-md-6">
					<label>Agency</label>
					<input v-model="item.agency" name="agency" type="number" min="0" class="form-control">
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
                :message="'Are you sure you want to archive Capacity #' + item.id + '?'"
                :alt-message="'Are you sure you want to restore Capacity #' + item.id + '?'"
                :disabled="loading"
                @load="load"
                @success="fetch"
                @error="fetch"
                ></action-button>
                
				<action-button type="submit" :disabled="loading" class="btn-primary" v-if="showBtn && item.allocation_id">Save Changes</action-button>
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
		computingRemainingCapacity() {

		},

		fetchSuccess(data) {
			this.item = data.item ? data.item : this.item;
			this.allocations = data.allocations ? data.allocations : this.allocations;
			this.total_capacity = data.total_capacity ? data.total_capacity : this.total_capacity;
			this.filtered_total_capacity = data.filtered_total_capacity ? data.filtered_total_capacity : this.filtered_total_capacity;
				
			this.usedCapacity = data.total_capacity;

			if(this.item) {
				this.usedAllocation = this.item.mgt_lgu + this.item.online + this.item.walk_in + this.item.agency;
			}

			this.computingRemainingCapacity();
			this.allocationChanged();
		},

		allocationChanged() {
			var id = this.item.allocation_id;
			var allocations = this.allocations;
			var total_allocation_capacity = this.total_capacity;

			_.each(allocations, (allocation) => {
				if(id == allocation.id) {
					this.destination_id = allocation.destination_id;
					this.capacity_per_day = parseInt(allocation.destination.capacity_per_day - total_allocation_capacity);
					this.total_destination_capacity = parseInt(allocation.destination.capacity_per_day);
				}
			});
		},
	},

	computed: {
		fetchParams() {
			return {id: this.destination_id};
		},

		remainingCapacity() {
			var remaining = 0;
			var online = this.item.online ? parseInt(this.item.online) : 0;
			var walk_in = this.item.walk_in ? parseInt(this.item.walk_in) : 0;
			var mgt_lgu = this.item.mgt_lgu ? parseInt(this.item.mgt_lgu) : 0;
			var agency = this.item.agency ? parseInt(this.item.agency) : 0;
			var unavailable_capacity = 0;

			let totalInputs = online + walk_in + mgt_lgu + agency;

			if(online >= this.total_destination_capacity || walk_in >= this.total_destination_capacity || mgt_lgu >= this.total_destination_capacity || agency >= this.total_destination_capacity) {
				this.showBtn = false;				
				return 0;
			}

			if(this.item.id) {
				remaining = (this.total_destination_capacity - this.usedCapacity);

				if(totalInputs >= this.usedAllocation) {
					totalInputs = totalInputs - this.usedAllocation;
					remaining = remaining - totalInputs;
				} else {
					remaining += (this.usedAllocation - totalInputs);
				}

				if(remaining < 0) {
					remaining = this.usedAllocation + remaining;
				}
			} else {

				if(this.filtered_total_capacity == null) {
					unavailable_capacity = (this.total_destination_capacity - this.usedCapacity) - (totalInputs);
				} else {
					unavailable_capacity = (this.total_destination_capacity - this.usedCapacity) - (totalInputs);
				}


				remaining = unavailable_capacity;
				
				// this.remainingCapacity = remaining;
			}

			if(remaining >= 0) {
				remaining = remaining;
			} else {
				this.max = 0;
			}

			if(remaining < 0) {
				remaining = 0;
				this.showBtn = false;
			} else {
				this.showBtn = true;
			}

			return remaining;		
		}
 	},

	watch: {
		'item.allocation_id'() {
			this.fetch();
		},

	},



	data() {
		return {
			item: {
				online: 0,
				mgt_lgu: 0,
				walk_in: 0,
				agency: 0,
			},
			allocations: [],
			destination_id: 0,
			total_capacity: 0,
			capacity_per_day: 0,
			total_destination_capacity: 0,
			// remainingCapacity: 0,
			usedCapacity: 0,
			showBtn: false,
			max: 0,
			usedAllocation: 0,
			canEdit: false
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