<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive } from 'vue';
import { getToday } from '@/common';
import axios from 'axios';
import Chart from '@/Components/Chart.vue';
import ResultTable from '@/Components/ResultTable.vue';

onMounted(() => {
    form.startDate = getToday();
    form.endDate = getToday();
})

const form = reactive({
    startDate: null,
    endDate: null,
    type: 'perDay',
    rfmPrms: [14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000]
})

const data = reactive({})

const getData = async () => {
    try {
        await axios.get('/api/analysis/', {
            params: {
                startDate: form.startDate,
                endDate: form.endDate,
                type: form.type,
                rfmPrms: form.rfmPrms,
            }
        }).then(res => {
            data.data = res.data.data;
            if(res.data.labels) data.labels = res.data.labels;
            data.totals = res.data.totals;
            data.type = res.data.type;
            if(res.data.eachCount) data.eachCount = res.data.eachCount;
            console.log(data);
        })
    } catch (e) {
        console.log(e.message);
    }
}
</script>

<template>
    <Head title="Data Analysis" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Analysis</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="getData">
                            Analysis Period<br>
                            <input type="radio" v-model="form.type" value="perDay" checked><span class="mr-2">daily</span>
                            <input type="radio" v-model="form.type" value="perMonth"><span
                                class="mr-2">Monthly</span>
                            <input type="radio" v-model="form.type" value="perYear"><span class="mr-2">Yearly</span>
                            <input type="radio" v-model="form.type" value="decile"><span class="mr-2">Decile Analysis</span>
                            <input type="radio" v-model="form.type" value="rfm"><span class="mr-2">RFM Analysis</span>
                            <br>
                            From: <input type="date" name="startDate" v-model="form.startDate">
                            To: <input type="date" name="endDate" v-model="form.endDate"><br>
                            <div v-if="form.type === 'rfm'" class="my-8">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>R (~ N days)</th>
                                            <th>F (N times ~)</th>
                                            <th>M (N JPY ~)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>5</td>
                                            <td><input type="number" v-model="form.rfmPrms[0]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[4]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[8]"></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td><input type="number" v-model="form.rfmPrms[1]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[5]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[9]"></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td><input type="number" v-model="form.rfmPrms[2]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[6]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[10]"></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td><input type="number" v-model="form.rfmPrms[3]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[7]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[11]"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button
                                class="mt-4 flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Analyze</button>
                        </form>
                        <div v-show="data.data">
                            <div v-if="data.type != 'rfm'">
                                <Chart :data="data" />
                            </div>
                            <ResultTable :data="data" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
