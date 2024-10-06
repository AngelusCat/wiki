<script setup>
    import {ref} from "vue";

    function changeVisibility()
    {
        importShow.value = !importShow.value;
        searchShow.value = !searchShow.value;
    }

    function send(e)
    {
        e.preventDefault();
        let response = fetch("http://wiki/import", {
            method: "POST",
            body: new FormData(e.currentTarget)
        });
    }

    let importShow = ref(true);
    let searchShow = ref(false);
</script>

<template>
    <button @click="changeVisibility">Import</button>
    <button @click="changeVisibility">Search</button>
    <div v-if="importShow">
        <p>{{ "import" }}</p>
        <form action="/import" method="post" @submit="send">
<!--            add csrf-->
            <input type="text" name="title" placeholder="ключевое слово">
            <button type="submit">Скопировать</button>
        </form>

    </div>
    <div v-if="searchShow">
        <p>{{ "search" }}</p>
    </div>
</template>

<style scoped>

</style>
