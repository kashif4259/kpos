<template>
    <div class="data-table">
        <div class="data-table-filters col-12 p-2">
            <div class="form-group row">
                <div class="col-6">
                <input id="productReports" v-model="searchFilter" placeholder="Search.." class="form-control">
                </div>
                <div class="col-6">
                    

                    <div class="dateRangeFilter">
                        <date-picker v-model="dateRange" range :format="'YYYY-MM-DD'" valueType="format" @change="dateRangeChanged"></date-picker>
                    </div>


                </div>
            </div>
        </div>
        <div class="main-table">
            <table class="table">
                <thead>
                    <tr>
                        <th class="table-head">#</th>
                        <th v-for="column in columns" :key="column" @click="sortByColumn(column)" class="table-head">
                            {{ column | columnHead }}
                            <span v-if="column === sortedColumn">
                                <i v-if="order === 'asc'" class="fa fa-caret-up"></i>
                                <i v-else class="fa fa-caret-down"></i>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="" v-if="tableData.length === 0">
                        <td class="lead text-center" :colspan="columns.length + 1">No data found.</td>
                    </tr>
                    <tr v-for="(data, key1) in tableData" :key="data.id" class="m-datatable__row" v-else>
                        <td>{{ serialNumber(key1) }}</td>
                        <td v-for="(value, key) in data">{{ value }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav v-if="pagination && tableData.length > 0">
            <ul class="pagination">
                <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                <!-- <li class="page-item" v-if="!(currentPage === 1)"> -->
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
                </li>
                <li v-for="page in pagesNumber" class="page-item"
                    :class="{ 'active': page == pagination.meta.current_page }">
                    <a href="javascript:void(0)" @click.prevent="changePage(page)" class="page-link">{{ page }}</a>
                </li>
                <li class="page-item" :class="{ 'disabled': currentPage === pagination.meta.last_page }">
                <!-- <li class="page-item" v-if="!(currentPage === pagination.meta.last_page)"> -->
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
                </li>
                <span style="margin-top: 8px; margin-right: 10px; margin-left: auto;"> &nbsp; <i>Displaying {{ pagination.data.length }} of {{
                        pagination.meta.total }} items</i></span>
            </ul>
        </nav>
    </div>
</template>


<script type="text/ecmascript-6">
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    components: { DatePicker },
    props: {
        fetchUrl: { type: String, required: true },
        columns: { type: Array, required: true },
    },
    data() {
        return {
            tableData: [],
            url: '',
            pagination: {
                meta: { to: 1, from: 1 }
            },
            offset: 9,
            currentPage: 1,
            limit: 10,
            sortedColumn: this.columns[0],
            order: 'asc',
            dateRange: null,
            searchFilter: "",
            startDateFilter: null,
            endDateFilter: null
        }
    },
    watch: {
        fetchUrl: {
            handler: function (fetchUrl) {
                this.url = fetchUrl
            },
            immediate: true
        },
        searchFilter: function (val) {
            this.fetchData()
        }
    },
    created() {
        return this.fetchData()
    },
    computed: {
        /**
         * Get the pages number array for displaying in the pagination.
         * */
        pagesNumber() {
            if (!this.pagination.meta.to) {
                return []
            }
            let from = this.pagination.meta.current_page - this.offset
            if (from < 1) {
                from = 1
            }
            let to = from + (this.offset * 2)
            if (to >= this.pagination.meta.last_page) {
                to = this.pagination.meta.last_page
            }
            let pagesArray = []
            for (let page = from; page <= to; page++) {
                pagesArray.push(page)
            }
            return pagesArray
        },
        /**
         * Get the total data displayed in the current page.
         * */
        totalData() {
            return (this.pagination.meta.to - this.pagination.meta.from) + 1
        }
    },
    methods: {
        fetchData() {
            let dataFetchUrl = `${this.url}?page=${this.currentPage}&limit=${this.limit}&column=${this.sortedColumn}&order=${this.order}&search=${this.searchFilter}&startDate=${this.startDateFilter}&endDate=${this.endDateFilter}`
            axios.get(dataFetchUrl)
                .then(({ data }) => {
                    this.pagination = data
                    this.tableData = data.data
                }).catch(error => this.tableData = [])
        },
        /**
         * Get the serial number.
         * @param key
         * */
        serialNumber(key) {
            return (this.currentPage - 1) * this.limit + 1 + key
        },
        /**
         * Change the page.
         * @param pageNumber
         */
        changePage(pageNumber) {
            this.currentPage = pageNumber
            this.fetchData()
        },
        /**
         * Sort the data by column.
         * */
        sortByColumn(column) {
            if (column === this.sortedColumn) {
                this.order = (this.order === 'asc') ? 'desc' : 'asc'
            } else {
                this.sortedColumn = column
                this.order = 'asc'
            }
            this.fetchData()
        },
        dateRangeChanged(){
            console.log("dateRange =>",this.dateRange);
            this.startDateFilter = this.dateRange[0];
            this.endDateFilter = this.dateRange[1];
            this.fetchData()
        },
        searchChanged(){
            this.fetchData()
        }
    },
    filters: {
        columnHead(value) {
            return value.split('_').join(' ').toUpperCase()
        }
    },
    name: 'DataTable'
}
</script>

<style scoped></style>