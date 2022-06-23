<template>
    <table-layout
        title="Chart STOCKS/Reordering Point"
        backRoute="inventory-report-branch.chart"
        note="Line chart shows the stocks and ROP of the branch."
    >
        <Head title="Chart STOCKS/Reordering Point" />
        <div class="bg-white shadow-lg rounded-md dark:bg-gray-700 p-4">
            <div class="flex gap-2 items-end">
                <div class="mb-0.5">
                    <Link :href="route('inventory-report-branch.index')">
                        <jet-button> Go to table </jet-button>
                    </Link>
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
            default: "line-inventory-chart",
        },
        datasetIdKey: {
            type: String,
            default: "line-inventory-dataset-id",
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
                        label: "Stocks",
                        data: this.stocks_quantity.map((item) => item.quantity),
                        borderColor: "rgb(252,165,165, 0.5)",
                        backgroundColor: ["rgb(239,68,68)"],
                    },
                    {
                        label: "Reordering Point",
                        data: this.stocks_quantity.map(
                            (item) => item.reordering_point
                        ),
                        backgroundColor: ["rgba(54, 162, 235)"],
                        borderColor: "rgb(54, 162, 235, 0.5)",
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

                this.$inertia.get(
                    route("inventory-report.line-chart"),
                    params,
                    {
                        preserveState: false,
                        replace: true,
                        preserveScroll: true,
                    }
                );
            }, 300),
            deep: true,
        },
    },
};
</script>

<style></style>
