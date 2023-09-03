<script setup>
import { getToday } from '@/common';
import { onMounted, reactive, ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  'customers': Array,
  'items': Array,
})

const form = reactive({
  date: null,
  customer_id: null,
  status: true,
  items: []
})

const itemList = ref([]);

onMounted(() => {
  form.date = getToday();
  props.items.forEach(item => {
    itemList.value.push({
      id: item.id,
      name: item.name,
      price: item.price,
      quantity: 0
    })
  })
})

const totalPrice = computed(() => {
  let total = 0;
  itemList.value.forEach(item => {
    total += item.price * item.quantity;
  })
  return total;
})

const storePurchase = () => {
  itemList.value.forEach( item => {
    if( item.quantity > 0 ) {
      form.items.push({
        id: item.id,
        quantity: item.quantity
      })
    }
  })
  console.log(itemList)
  Inertia.post(route('purchases.store'), form)
}

const quantity = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

</script>

<template>
  <form @submit.prevent="storePurchase">
  Date<br>
  <input type="date" name="date" v-model="form.date"><br>
  Name<br>
  <select name="customer" v-model="form.customer_id">
    <option v-for="customer in customers" :value="customer.id" :key="customer.id">
      {{ customer.id }} : {{ customer.name }}
    </option>
  </select><br>
  Item, Service<br>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Sum</th>
      </tr>
    </thead>
    <tr v-for="item in itemList">
      <td>{{  item.id }}</td>
      <td>{{  item.name }}</td>
      <td>{{  item.price }}</td>
      <td>
        <select name="quantity" v-model="item.quantity">
          <option v-for="q in quantity" :value="q">{{ q }}</option>
        </select>
      </td>
      <td>
        {{ item.price * item.quantity }}
      </td>
    </tr>
  </table>
  <br>
  Total Price: {{ totalPrice }} JPY
  <br>
  <button>Submit</button>
  </form>
</template>