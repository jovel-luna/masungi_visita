<template>
    <div>
        <!-- DATATABLE -->
        <data-table
            class="doublescroll__con"
            ref="data-table"
            :headers="headers"
            :filters="filters"
            :fetch-url="fetchUrl"
            :no-action="noAction"
            :disabled="disabled"
            :per-page="20"
            order-by="id"
            order-desc
            @load="load"
        >
            <template v-slot:body="{ items }">
                <tr v-for="item in items" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.url }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>
                        <view-button :url="item.media_url" />
                    </td>
                </tr>
            </template>
        </data-table>

        <loader :loading="loading"></loader>
    </div>
</template>

<script>
import ListMixin from "Mixins/list.js";
import NumberFormat from "Mixins/number.js";

import SearchForm from "Components/forms/SearchForm.vue";
import ActionButton from "Components/buttons/ActionButton.vue";
import ViewButton from "Components/buttons/ViewButton.vue";
import Select from "Components/inputs/Select.vue";

export default {
    computed: {
        headers() {
            let array = [
                { text: "Ref ID", value: "id" },
                { text: "Media Name", value: "media_name" },
                { text: "Media URL", value: "media_url" }
            ];

            array = array.concat([{ text: "Created At", value: "created_at" }]);

            return array;
        }
    },

    mixins: [ListMixin, NumberFormat],
    components: {
        "search-form": SearchForm,
        "view-button": ViewButton,
        "action-button": ActionButton,
        selector: Select
    }

};
</script>
