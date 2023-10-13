<script setup>
const props = defineProps({
  "data": Object
});

</script>

<template>
  <div v-if="data.type === 'perDay' || data.type === 'perMonth' || data.type === 'perYear'" class="lg:w-2/3 w-full mx-auto overflow-auto">
    <table class="table-auto w-full text-left whitespace-no-wrap">
      <thead>
        <tr>
          <th
            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
            Date</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
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
  <div v-if="data.type === 'decile'" class="lg:w-2/3 w-full mx-auto overflow-auto">
    <table class="table-auto w-full text-left whitespace-no-wrap">
      <thead>
        <tr>
          <th
            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
            Decile Group</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
            Average Price</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
            Total Price</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
            Ratio</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in data.data" :key="item.endDate">
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.decile }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.average }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.totalPerGroup }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.totalRatio }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div v-if="data.type === 'rfm'" class="lg:w-2/3 w-full mx-auto overflow-auto">
    <h2 class="text-cengter text-2xl">RFM Analysis Result</h2>
    <div class="my-4 text-center">{{ data.totals }}</div>
    <h3 class="text-center text-2xl my-4">RFM total users per rank</h3>
    <table class="table-auto w-full text-left whitespace-no-wrap">
      <thead>
        <tr>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Rank</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">R</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">M</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="rfm in data.eachCount" :key="rfm.rank">
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.rank }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.r }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.f }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.m }}</td>
        </tr>
      </tbody>
    </table>
    <h3 class="text-center text-2xl my-4">R and F analysis result</h3>
    <table class="table-auto w-full text-left whitespace-no-wrap">
      <thead>
        <tr>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">rRank</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_5</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_4</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_3</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_2</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_1</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="rf in data.data" :key="rf.rRank">
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.rRank }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_5 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_4 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_3 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_2 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_1 }}</td>
        </tr>
      </tbody>
    </table>

  </div>
</template>