<script setup>
    import {ref} from "vue";

    let props = defineProps(["csrfToken"]);
    let importShow = ref(true);
    let searchShow = ref(false);
    let articleShow = ref(false);
    let article = ref({});
    let articles = ref([]);

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
            articles.value.push(article.value);
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
            <button type="reset">Очистить</button>
        </form>
        <div v-if="articleShow">
            <p>Импорт завершен.</p>
            <p>Найдена статья по адресу: {{ article.link }}</p>
            <p>Время обработки: {{ article.processingTime + " с."}}</p>
            <p>Размер статьи: {{ article.size + " б."}}</p>
            <p>Количество слов: {{ article.wordCount }}</p>
        </div>
        <div v-if="articles.length !== 0">
            <table border>
                <tr>
                    <td>Название статьи</td>
                    <td>Ссылка</td>
                    <td>Размер статьи</td>
                    <td>Количество слов</td>
                </tr>
                <tr v-for="article in articles">
                    <td>{{ article.title }}</td>
                    <td>{{ article.link }}</td>
                    <td>{{ article.size }}</td>
                    <td>{{ article.wordCount }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div v-if="searchShow">
        <p>{{ "search" }}</p>
    </div>
</template>

<style scoped>

</style>
