<template>
    <div>
        <!-- DATATABLE -->
        <data-table
            class="doublescroll__con"
            ref="data-table"
            :headers="headers"
            :filters="filters"
            :disabled="disabled"
            :per-page="20"
            order-by="id"
            order-desc

            :items="media"
        >
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

    data() {
        return {
            media: [
                { id: 1, media_name: "Name 1", media_url: "url1", created_at: "2023-01-01" },
                { id: 2, media_name: "Name 2", media_url: "url2", created_at: "2023-01-02" },
                { id: 3, media_name: "Name 3", media_url: "url3", created_at: "2023-01-03" }
            ],
            // filters: {},
            // fetchUrl: "/api/media",
            // noAction: false,
            // disabled: false,
            // loading: false
        };
    },

    mixins: [ListMixin, NumberFormat],
    components: {
        "search-form": SearchForm,
        "view-button": ViewButton,
        "action-button": ActionButton,
        selector: Select
    },

    // methods: {
    //     load() {
    //         // Implement the data loading logic here
    //     }
    // }
};
</script>
