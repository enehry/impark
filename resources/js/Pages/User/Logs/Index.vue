<template>
    <table-layout title="User activity logs/BRANCH">
        <Head title="User activity logs" />
        <div class="py-4">
            <div
                class="flex md:flex-row flex-col gap-2 items-center justify-between mb-8 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-lg"
            >
                <div class="flex items-center gap-2">
                    <a
                        :href="route('user-logs.download.pdf', logs_params)"
                        class="text-red-500 hover:text-red-300 uppercase text-xs flex items-center"
                    >
                        <DocumentDownloadIcon class="w-6 h-6" />
                        <div class="text-gray-500">PDF</div>
                    </a>
                    <a
                        :href="route('user-logs.download.excel', logs_params)"
                        class="text-green-500 hover:text-green-300 uppercase text-xs flex items-center"
                    >
                        <DocumentDownloadIcon class="w-6 h-6" />
                        <div class="text-gray-500">Excel</div>
                    </a>
                </div>
                <div
                    class="flex flex-col sm:flex-row items-center justify-end gap-2"
                >
                    <div>
                        <div class="flex items-center">
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
                                    v-model="logs_params.startDate"
                                    name="start"
                                    type="date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 px-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Select date start"
                                />
                            </div>
                            <span class="mx-2 text-gray-500">-</span>
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
                                    v-model="logs_params.endDate"
                                    name="end"
                                    type="date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 px-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Select date end"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label
                            for="default-search"
                            class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-gray-300"
                            >Search</label
                        >
                        <div class="relative">
                            <div
                                class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
                            >
                                <svg
                                    class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    ></path>
                                </svg>
                            </div>
                            <input
                                v-model="logs_params.search"
                                type="search"
                                id="small-search"
                                class="block px-4 pl-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search activity Logs..."
                                required
                            />
                        </div>
                        <div>
                            <JetButton @click.prevent="filterLogs"
                                >GO</JetButton
                            >
                        </div>
                    </div>
                </div>
            </div>
            <ol
                v-if="hasData"
                class="relative border-l ml-4 border-gray-200 dark:border-gray-700"
            >
                <row-logs v-for="log in allLogs" :key="log.id" :log="log">
                </row-logs>
            </ol>
            <div v-else>
                <Empty label="User activity logs" />
            </div>
        </div>
        <div v-show="isLoading" class="text-center pb-4">
            <svg
                role="status"
                class="inline w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                viewBox="0 0 100 101"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor"
                />
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill"
                />
            </svg>
        </div>
        <pagination :links="logs.links" />
        <!-- <span ref="loadMoreIntersect" />
        <div
            class="text-center uppercase font-medium text-gray-500 pb-8"
            v-if="this.logs.next_page_url === null"
        >
            End of logs
        </div> -->
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Head } from "@inertiajs/inertia-vue3";
import RowLogs from "./RowLogs.vue";
import JetButton from "@/Jetstream/Button.vue";
import pickBy from "lodash/pickBy";
import Pagination from "@/Components/Pagination.vue";
import Empty from "@/Components/Empty.vue";
import { DocumentDownloadIcon } from "@heroicons/vue/solid";
export default {
    components: {
        TableLayout,
        Head,
        RowLogs,
        JetButton,
        Pagination,
        Empty,
        DocumentDownloadIcon,
    },
    props: {
        logs: {
            type: Object,
            default: () => {},
        },
        filter_logs: {
            type: Object,
            default: () => {},
        },
    },
    mounted() {
        // const observer = new IntersectionObserver((entries) =>
        //     entries.forEach(
        //         (entry) => entry.isIntersecting && this.loadMoreLogs(),
        //         {
        //             rootMargin: "-150px 0px 0px 0px",
        //         }
        //     )
        // );
        // observer.observe(this.$refs.loadMoreIntersect);
    },
    data() {
        return {
            allLogs: this.logs.data,
            initialUrl: this.$page.url,
            isLoading: false,
            logs_params: {
                search: this.filter_logs.search,
                startDate: this.filter_logs.startDate,
                endDate: this.filter_logs.endDate,
            },
        };
    },
    computed: {
        hasData() {
            return this.allLogs.length > 0;
        },
    },
    methods: {
        filterLogs() {
            let params = pickBy(this.logs_params);

            this.isLoading = true;
            this.$inertia.get(route("user-logs.index"), params, {
                // preserveState: true,
                // preserveScroll: true,
                replace: true,
                // only: ["logs", "filter_logs"],
                onSuccess: () => {
                    // this.allLogs = [...this.allLogs, ...this.logs.data];
                    // window.history.replaceState(
                    //     {},
                    //     this.$page.title,
                    //     this.initialUrl
                    // );
                    this.isLoading = false;
                },
            });
        },
        // loadMoreLogs() {
        //     if (this.logs.next_page_url === null) {
        //         return;
        //     }

        //     let params = pickBy(this.logs_params);
        //     this.isLoading = true;
        //     this.$inertia.get(this.logs.next_page_url, params, {
        //         preserveState: true,
        //         preserveScroll: true,
        //         only: ["logs", "filter_logs"],
        //         onSuccess: () => {
        //             this.allLogs = [...this.allLogs, ...this.logs.data];
        //             window.history.replaceState(
        //                 {},
        //                 this.$page.title,
        //                 this.initialUrl
        //             );
        //             this.isLoading = false;
        //         },
        //     });
        // },
    },
};
</script>

<style></style>
