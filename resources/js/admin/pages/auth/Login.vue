<script setup lang="ts">
import GuestLayout from '@/admin/Layouts/GuestLayout.vue';
import { store } from '@/routes/admin';
import { Form, usePage } from '@inertiajs/vue3';

const page = usePage();
const props = page.props;
</script>
<template>
  <GuestLayout>
    <div class="flex w-1/4 items-center">
      <div class="ml-12 w-lg md:space-y-6">
        <h1
          class="text-xl leading-tight font-bold tracking-tight text-gray-900 md:text-2xl dark:text-white"
        >
          Sign in to your account
        </h1>
        <Form v-bind="store.post()" class="space-y-4 md:space-y-6" action="#">
          <div>
            <label
              for="email"
              class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
              >Your email</label
            >
            <input
              type="email"
              name="email"
              id="email"
              class="focus:border-primary-600 block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-gray-900 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
              placeholder="name@company.com"
              required
            />
          </div>
          <div>
            <label
              for="password"
              class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
              >Password</label
            >
            <input
              type="password"
              name="password"
              id="password"
              placeholder="••••••••"
              class="focus:border-primary-600 block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-gray-900 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
              required
            />
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-start">
              <div class="flex h-5 items-center">
                <input
                  id="remember"
                  aria-describedby="remember"
                  type="checkbox"
                  class="dark:focus:ring-primary-600 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                  required
                />
              </div>
              <div class="ml-3 text-sm">
                <label for="remember" class="text-gray-500 dark:text-gray-300"
                  >Remember me</label
                >
              </div>
            </div>
            <a
              href="#"
              class="dark:text-primary-500 text-sm font-medium text-blue-600 hover:underline"
              >Forgot password?</a
            >
          </div>
          <button
            type="submit"
            class="hover:bg-primary-700 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 w-full rounded-lg bg-blue-600 px-5 py-2.5 text-center text-sm font-medium text-white focus:ring-4 focus:outline-none"
          >
            Sign in
          </button>
        </Form>
      </div>
    </div>
    <div
      class="flex w-3/4 bg-cover bg-center"
      :style="`background-image: url('${$page.props.settings.backgroundImage ?? '/images/login-background.webp'}')`"
    >
      <div
        class="z-50 h-full w-[15%] bg-linear-to-r from-background to-transparent to-90%"
      ></div>
      {{ $page.props }}
    </div>
  </GuestLayout>
</template>
