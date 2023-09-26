<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive } from 'vue';
import { getToday } from '@/common';
import axios from 'axios';
import Chart from '@/Components/Chart.vue';

onMounted(() => {
    form.startDate = getToday();
    form.endDate = getToday();
})

const form = reactive({
    startDate: null,
    endDate: null,
    type: 'perDay'
})

const data = reactive({})

const getData = async () => {
    try {
        await axios.get('/api/analysis/', {
            params: {
                startDate: form.startDate,
                endDate: form.endDate,
                type: form.type
            }
        }).then(res => {
            data.data = res.data.data;
            data.labels = res.data.labels;
            data.totals = res.data.totals;
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
                            From: <input type="date" name="startDate" v-model="form.startDate">
                            To: <input type="date" name="endDate" v-model="form.endDate"><br>
                            <button
                                class="mt-4 flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Analyze</button>
                        </form>
                        <div v-show="data.data">
                        <Chart :data="data" />
                        </div>
                        <div v-show="data.data" class="lg:w-2/3 w-full mx-auto overflow-auto">
                            <table class="table-auto w-full text-left whitespace-no-wrap">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                                            Date</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in data.data" :key="item.endDate">
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.date }}</td>
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</AuthenticatedLayout></template>