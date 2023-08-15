<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue';
//import {onMounted} from 'vue';

defineProps({
  customers: Object
})

//onMounted(() => {
//  console.log(propos.customers)
//})
</script>


<template>

  <ul v-for="customer in customers.data" :key="customer.id">
    <li>{{ customer.id }}</li>
    <li>{{ customer.name }}</li>
  </ul>>
  <Pagination class="mt-6" :links="customers.links"></Pagination>
 <!--
  <Head title="Customer List" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customer List</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <section class="text-gray-600 body-font">
              <div class="container px-5 py-8 mx-auto">
                <FlashMessage />
                <div class="flex pl-4 my-4 lg:w-2/3 w-full mx-auto">
                  <Link as="button" :href="route('customers.create')" class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Register a Customer</Link>
                </div>
                <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                  <table class="table-auto w-full text-left whitespace-no-wrap">
                    <thead>
                      <tr>
                        <th
                          class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                          ID</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                          Name</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                          Kana</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                          Phone</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="customer in customers" :key="customer.id">
                        <td class="border-b-2 border-gray-200 px-4 py-3">
                          {{ customer.id  }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ customer.name  }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ customer.kana  }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ customer.tel  }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</AuthenticatedLayout>
-->
</template>
