<script setup>
    import {ref} from "vue";

    let props = defineProps(["csrfToken"]);
    let importShow = ref(true);
    let searchShow = ref(false);
    let articleShow = ref(false);
    let article = ref();
    function changeVisibility()
    {
        importShow.value = !importShow.value;
        searchShow.value = !searchShow.value;
    }

    async function send(e)
    {
        e.preventDefault();
        let response = await fetch("http://wiki/import", {
            method: 'POST',
            body: new FormData(e.currentTarget)
        });
        article.value = await response.json();
        if (Object.keys(article).length !== 0) {
            articleShow.value = true;
        }
    }
</script>

<template>
    <button @click="changeVisibility">Import</button>
    <button @click="changeVisibility">Search</button>
    <div v-if="importShow">
        <p>{{ "import" }}</p>
        <form action="/import" method="post" @submit="send">
            <input type="hidden" name="_token" :value="props.csrfToken">
            <input type="text" name="title" placeholder="ключевое слово">
            <button type="submit">Скопировать</button>
        </form>
        <div v-if="articleShow">
            <p>Импорт завершен.</p>
            <p>Найдена статья по адресу: {{ article.link }}</p>
            <p>Время обработки: {{ article.processingTime + " с."}}</p>
            <p>Размер статьи: {{ article.size + " б."}}</p>
            <p>Количество слов: {{ article.wordCount }}</p>
        </div>
    </div>
    <div v-if="searchShow">
        <p>{{ "search" }}</p>
    </div>
</template>

<style scoped>

</style>
