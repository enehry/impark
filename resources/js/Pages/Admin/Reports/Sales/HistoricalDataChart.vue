<template>
    <table-layout
        title="Chart Sales Report"
        backRoute="sales-report.index"
        note="You can choose a specific sales of the branch by 
        clicking the selection dropdown or filter by daily, weekly, monthly and yearly.
        you can save the chart by right click and save as image."
    >
        <Head title="Chart Sales Report" />
        <div class="bg-white shadow-lg rounded-md dark:bg-gray-700 p-4">
            <div
                class="flex sm:flex-row flex-col gap-2 justify-between items-end"
            >
                <div class="flex items-end gap-2">
                    <div>
                        <label
                            for="countries"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                            >Select a branch</label
                        >
                        <select
                            v-model="params.branch_id"
                            id="branches"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected :value="null">All Branch</option>
                            <option
                                v-for="branch in sales_chart_branches"
                                :key="branch.id"
                                :value="branch.id"
                            >
                                {{ branch.name }}
                            </option>
                        </select>
                    </div>
                    <div class="">
                        <label for="sales-report" class="sr-only">Type</label>
                        <select
                            v-model="params.group_by"
                            class="bg-gray-50 w-full sm:w-min border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected :value="null">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between sm:w-min w-full">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                        <input
                            v-model="params.start_date"
                            name="start"
                            type="date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Select date start"
                        />
                    </div>
                    <span class="mx-4 text-xs text-gray-500">to</span>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                        <input
                            v-model="params.end_date"
                            name="end"
                            type="date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Select date end"
                        />
                    </div>
                </div>
            </div>
            <div class="py-8">
                <Line
                    :chart-options="chartOptions"
                    :chart-data="chartData"
                    :chart-id="chartId"
                    :dataset-id-key="datasetIdKey"
                    :plugins="plugins"
                    :css-classes="cssClasses"
                    :styles="styles"
                    :width="width"
                    :height="height"
                />
            </div>
        </div>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import { Line } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement
);

export default {
    components: {
        TableLayout,
        Head,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        JetInputError,
        Line,
        Link,
    },
    props: {
        sales_chart_data: {
            type: Array,
            default: () => [],
        },
        sales_chart_branches: {
            type: Array,
            default: () => [],
        },
        sales_chart_filter: {
            type: Object,
            default: () => ({}),
        },
        chartId: {
            type: String,
            default: "line-sales-chart",
        },
        datasetIdKey: {
            type: String,
            default: "line-sales-dataset-id",
        },
        width: {
            type: Number,
            default: 400,
        },
        height: {
            type: Number,
            default: 200,
        },
        cssClasses: {
            default: "",
            type: String,
        },
        styles: {
            type: Object,
            default: () => {},
        },
        plugins: {
            type: Object,
            default: () => {},
        },
    },
    mounted() {},
    data() {
        return {
            params: {
                branch_id: this.sales_chart_filter.branch_id,
                group_by: this.sales_chart_filter.group_by,
                start_date: this.sales_chart_filter.start_date,
                end_date: this.sales_chart_filter.end_date,
            },
            chartData: {
                labels: this.sales_chart_data.map((item) => {
                    return item.formatted_date;
                }),
                datasets: [
                    {
                        data: this.sales_chart_data.map((item) => {
                            return item.total_sales;
                        }),
                        label: "Sales",
                        borderColor: "rgb(252,165,165, 0.5)",
                        backgroundColor: ["rgb(239,68,68)"],
                        tension: 0.1,
                    },
                ],
            },
            chartOptions: {
                parsing: {},

                // label position
                plugins: {
                    legend: {
                        display: true,
                        maxHeight: 50,
                        fullSize: false,
                        position: "top",
                        align: "start",
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            boxHeight: 10,
                        },
                    },
                },
                layout: {
                    padding: {},
                },
            },
        };
    },
    methods: {
        changeChartStyle() {},
    },
    watch: {
        params: {
            handler: throttle(function () {
                let params = pickBy(this.params);

                this.$inertia.get(route("sales-report.chart"), params, {
                    preserveState: false,
                    replace: true,
                    preserveScroll: true,
                });
            }, 300),
            deep: true,
        },
    },
};
</script>

<style></style>
