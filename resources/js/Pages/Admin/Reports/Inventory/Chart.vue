<template>
    <table-layout
        title="Inventory Report Pie Chart"
        backRoute="inventory-report.index"
        note="You can choose a specific count/percentage of a branch by selecting it in the dropdown."
    >
        <Head title="Inventory Report Pie Chart" />
        <div class="bg-white shadow-lg rounded-md dark:bg-gray-700 p-4">
            <div class="flex gap-2 items-end">
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
                            v-for="branch in inventory_branches"
                            :key="branch.id"
                            :value="branch.id"
                        >
                            {{ branch.name }}
                        </option>
                    </select>
                </div>
                <div class="mb-0.5">
                    <Link :href="route('inventory-report.index')">
                        <jet-button> Go to table </jet-button>
                    </Link>
                </div>
            </div>
            <div>
                <Doughnut
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
import { Doughnut } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    LinearScale,
    BarElement,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    LinearScale,
    BarElement
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
        Doughnut,
        Link,
    },
    props: {
        stocks_quantity: {
            type: Array,
            default: () => [],
        },
        inventory_branches: {
            type: Array,
            default: () => [],
        },
        inventory_filter: {
            type: Object,
            default: () => ({}),
        },
        chartId: {
            type: String,
            default: "pie-inventory-chart",
        },
        datasetIdKey: {
            type: String,
            default: "pie-inventory-dataset-id",
        },
        width: {
            type: Number,
            default: 400,
        },
        height: {
            type: Number,
            default: 400,
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
            chartType: "doughnut",
            params: {
                branch_id: this.inventory_filter.branch_id,
            },
            chartData: {
                labels: this.stocks_quantity.map(
                    (item) => item.name + " (" + item.quantity + ")"
                ),
                datasets: [
                    {
                        data: this.stocks_quantity.map((item) => item.quantity),
                        backgroundColor: [
                            "rgba(255, 99, 132, 0.8)",
                            "rgba(54, 162, 235, 0.8)",
                            "rgba(255, 206, 86, 0.8)",
                            "rgba(75, 192, 192, 0.8)",
                            "rgba(153, 102, 255, 0.8)",
                            "rgba(218, 247, 166, 0.8)",
                            "rgba(255, 195, 0, 0.8)",
                            "rgba(199, 0, 57, 0.8)",
                            "rgba(144, 12, 63, 0.8)",
                            "rgba(88, 24, 69, 0.8)",
                        ],
                    },
                ],
            },
            chartOptions: {
                parsing: {},

                // label position
                plugins: {
                    legend: {
                        maxHeight: 50,
                        fullSize: false,
                        position: "right",
                        align: "start",
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            boxHeight: 10,
                        },
                        title: {
                            display: true,
                            text: "PRODUCTS-STOCKS",
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

                this.$inertia.get(route("inventory-report.chart"), params, {
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
