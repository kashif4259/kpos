<template>
    <div>
        <div class="main-layout-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent m-0">
                    <li class="breadcrumb-item">
                        <span>Product Reports</span>
                    </li>
                </ol>
            </nav>
            <div class="main-layout-card">
                <!-- <div class="custom-tabs">
                    <ul class="nav nav-tabs">
                        <li class="nav-item d-flex justify-content-center">
                            
                        </li>
                    </ul>
                </div> -->

                <div class="row mb-3 p-3">
                                <div class="col-6">

                                    <label class="form-label">Type: </label>
                                    <select class="custom-select  form-select-sm"  v-model="selectedReportType"
                                        @change="onChangeReportType($event)">
                                        <option v-for="report in reportTypes"
                                            v-bind:value="{ value: report.value, label: report.label }">
                                            {{ report.label }}
                                        </option>
                                    </select>
                                </div>
                </div>

                <div v-if="selectedReportType.value == 'profit_loss'" class="product-reports" style="display: block;">
                    <datatable-profitLoss :fetch-url="fetchUrlProfitLoss" :columns="profit_loss_columns">
                    </datatable-profitLoss>

                </div>

                <div v-if="selectedReportType.value == 'inventory'" class="product-reports" style="display: block;">
                    <datatable-inventory :fetch-url="fetchUrlInventory" :columns="inventory_columns">
                    </datatable-inventory>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import Axios from "axios";
import axiosGetPost from "../../helper/axiosGetPostCommon";
import DatatableProfitLoss from "./datatableProfitLoss.vue";
import DatatableInventory from "./datatableInventory.vue";

export default {
    components: { DatatableProfitLoss, DatatableInventory },
    extends: axiosGetPost,
    props: [
    ],
    data() {
        return {

            // datatable:{
            //     columns:['id', 'title','quantity','purchase_total','sold_total','profit','loss'],
            //     fetchUrl: "/product-reports-data/profit_loss",
            // },
            response: [],
            profit_loss_columns: ['id', 'title', 'quantity', 'purchase_total', 'sold_total', 'profit', 'loss'],
            inventory_columns: ['id', 'title', 'available', 'sold', 'total'],
            selectedReportType: {
                value: "profit_loss",
                label: "Profit Loss"
            },
            reportTypes: [
                {
                    value: "profit_loss",
                    label: "Profit Loss"
                },
                {
                    value: "inventory",
                    label: "Product Inventory"
                }
            ],
            fetchUrlProfitLoss: window.appConfig.appUrl+"/product-reports-data/profit_loss",
            fetchUrlInventory: window.appConfig.appUrl+"/product-reports-data/inventory",
        }
    },
    methods: {
        getData: function () {

            // const type = this.selectedReportType.value;
            // Axios.get('/product-reports-data/'+type).then(function (response) {
            //     console.log("--- response.data =>", response.data);
            //     console.log("--- response.data.data =>", response.data.data);

            //     this.response = response.data.data;
            // });
        },
        onChangeReportType(event) {
            // console.log(event.target.value);
            // console.log(this.selectedReportType);
        }

    },
    created: function () {
        // console.log('Created.')
        this.getData()
    },

    mounted() {
        console.log('Component mounted.')

    }
};
</script>
<style></style>