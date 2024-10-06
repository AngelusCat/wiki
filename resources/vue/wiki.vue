<script setup>
    import {ref} from "vue";

    function changeVisibility()
    {
        importShow.value = !importShow.value;
        searchShow.value = !searchShow.value;
    }

    let importShow = ref(true);
    let searchShow = ref(false);
</script>

<template>
    <button @click="changeVisibility">Import</button>
    <button @click="changeVisibility">Search</button>
    <div v-if="importShow">
        <p>{{ "import" }}</p>
    </div>
    <div v-if="searchShow">
        <p>{{ "search" }}</p>
    </div>
</template>

<style scoped>

</style>
