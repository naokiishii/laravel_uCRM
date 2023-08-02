<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const newTitle = ref('');
const newContent = ref('');
</script>

<template>
  This is an Inertia Test Page.<br>
  <a href="/">via a tag</a><br>
  <Link href="/">via Link</Link>
  <!-- ":" means v-bind which indicates paramter is dynamic not static -->
  <Link :href="route('inertia.index')">Link with name</Link><br>
  <Link :href="route('inertia.show', { id: 50 })">Route Parameter Test</Link><br>
  <div class="mb-8">
  <input type="text" name="newTitle" v-model="newTitle">{{ newTitle }}<br>
  <input type="text" name="newContent" v-model="newContent">{{ newContent }}<br>
  <Link as="button" method="post" :href="route('inertia.store')" :data="{ title: newTitle, content: newContent }">Store
  Test</Link>
  </div>
</template>