<template>
    <div>
        <div v-if="getWeekDay(selectedDate) == 1">
            <div class="row" >
                <div class="custom-control custom-switch ml-3">
                    <input
                    name="is_available" :checked="isAvailable == 0" type="checkbox" class="custom-control-input" @load="load" id="is_available" @change="makeAvailable(selectedDate)">
                    <label class="custom-control-label" for="is_available">Is this available for reservation?</label>
                </div>
            </div>
            <br>
        </div>
        <filter-box @refresh="fetch">
            <template v-slot:left>
            </template>
            <template v-slot:right>

                <search-form
                @search="filter($event, 'search')">
                </search-form>

            </template>
        </filter-box>

        <!-- DATATABLE -->
        <data-table
        ref="data-table"
        :headers="headers"
        :filters="filters"
        :fetch-url="fetchUrl"
        :no-action="noAction"
        :disabled="disabled"
        order-by="id"
        order-desc
        @fetch="fetch"
        >

            <template v-slot:body="{ items }">
                <tr v-for="item in items">
                    <td>{{ item.id }}</td>
                    <td>
                        <a :href="item.qr_path" target="_blank">
                            <img :src="item.qr_path" width="100px" class="rounded img-fluid img-thumbnail">
                        </a>
                    </td>
                    <td>{{ item.qr_id }}</td>
                    <td>{{ item.main_contact.fullname }}</td>
                    <td>{{ item.agency_code }}</td>
                    <td>{{ item.total_guest }}</td>
                    <td>{{ item.time }}</td>
                    <td>{{ item.allocation }}</td>
                    <td>{{ item.main_contact.type }}</td>
                    <td>{{ item.is_walkin }}</td>
                    <td>{{ item.status }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>
                        <view-button :href="item.showUrl+'/'+selectedDate+'/'+destination+'/'+experience+'/'+destinationName"></view-button>
                        
                        <action-button
                        v-if="!hideButtons"
                        small 
                        color="btn-danger"
                        alt-color="btn-warning"
                        :show-alt="item.deleted_at"
                        :action-url="item.archiveUrl"
                        :alt-action-url="item.restoreUrl"
                        icon="fas fa-trash"
                        alt-icon="fas fa-trash-restore-alt"
                        confirm-dialog
                        :disabled="loading"
                        title="Archive Item"
                        alt-title="Restore Item"
                        :message="'Are you sure you want to archive this reservation? '"
                        :alt-message="'Are you sure you want to restore this reservation?'"
                        @load="load"
                        @success="sync"
                        ></action-button>
                    </td>
                </tr>
            </template>

        </data-table>

        <loading :active.sync="isLoading"
		        :is-full-page="fullPage" />

        <loader :loading="loading"></loader>
    </div>
</template>

<script type="text/javascript">
import ListMixin from '../../../mixins/list.js';

import SearchForm from '../../../components/forms/SearchForm.vue';
import ActionButton from '../../../components/buttons/ActionButton.vue';
import ViewButton from '../../../components/buttons/ViewButton.vue';
import moment from 'moment';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css'

export default {
    data() {
        return {
            isLoading: false,
            fullPage: true
        }
    },
    computed: {
        headers() {
            let array = [
                { text: '#', value: 'id' },
                { text: 'QR', value: 'qr_id' },
                { text: 'QR ID', value: 'qr_id' },
                { text: 'Point Person', value: 'fullname' },
                { text: 'Agency Code', value: 'agency_code' },
                { text: 'Total Guest', value: 'total_guest' },
                { text: 'Time', value: 'time' },
                { text: 'Experience', value: 'allocation' },
                { text: 'Type', value: 'type' },
                { text: 'Walk In', value: 'is_walkin' },
                { text: 'Status', value: 'status' },
            ];


            array = array.concat([
                { text: 'Created Date', value: 'created_at' },
            ]);

            return array;
        },
    },

    methods: {
        fetch() {
		console.log('fetching');
            $('.doublescroll__con .table-responsive').doubleScroll({resetOnWindowResize: true});
        },
        getWeekDay(date) {
            const getweek = moment(date).format('E');
            return getweek // (1-7) Monday - Sunday
        },
        makeAvailable(date) {
            this.isLoading = true;
            var data = {
                date: date,
            };
            
            axios.post(this.changeUrl, data)
                .then((response) => {
                    this.isLoading = false;
                    var res = response;
                    console.log(res)
                    if(res.data.is_available == 0){
                        swal.fire({
                            title: 'Updated successfully',
                            text: res.data.date + ' is now available for visit request',
                            icon: 'success',
                            showCancelButton: false,
                            reverseButtons: true,
                            confirmButtonText: 'Confirm'
                        }).then(response => {
                            
                        })
                    }else{
                        swal.fire({
                            title: 'Updated successfully',
                            text: res.data.date + ' is now unavailable for visit request',
                            icon: 'success',
                            showCancelButton: false,
                            reverseButtons: true,
                            confirmButtonText: 'Confirm'
                        }).then(response => {
                            
                        })
                    }
                })
            
        }
    },

    props: {
        hideParent: {
            default: false,
            type: Boolean,
        },

        hideButtons: {
            default: false,
            type: Boolean,
        },

        selectedDate: String,
        destination: String,
        experience: String,
        destinationName: String,
        changeUrl: String,
        isAvailable: String
    },

    mixins: [ ListMixin ],

    components: {
        'search-form': SearchForm,
        'view-button': ViewButton,
        'action-button': ActionButton,
        Loading
    },
}
</script>
