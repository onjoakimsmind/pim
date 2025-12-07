<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Wrapper from './Wrapper.vue';
</script>
<template>
  <Head>
    <title>Admin Panel</title>
  </Head>
  <Wrapper>
    <div class="flex h-screen w-full">
      <slot />
    </div>
  </Wrapper>
</template>
