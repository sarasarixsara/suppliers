<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen">
    <div class="bg-blue w-full h-20 top-0 overflow-hidden"></div>
    <div class=" flex flex-col sm:justify-center items-center pt-6 sm:pt-4 bg-gray-100">

        <div>
            <Link href="/">
<!--                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />-->
                <img src="/images/cci-logo.png" >
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>

    </div>
    </div>
</template>
