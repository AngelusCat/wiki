<script setup>
    import {ref} from "vue";

    let props = defineProps(["csrfToken"]);

    let importShow = ref(true);
    let searchShow = ref(false);
    let articleShow = ref(false);
    let searchResultShow = ref(false);
    let contentShow = ref(false);
    let errorShow = ref(false);

    let article = ref({});
    let articles = ref({});
    let searchResult = ref({});
    let content = ref("");
    let error = ref("");

    let startArticleId = 1;
    let endArticleId = 10;

    async function init()
    {
        let response = await fetch("http://wiki/articles/" + startArticleId + "/" + endArticleId);
        articles.value = await response.json();
    }
    init();

    function add()
    {
        startArticleId = endArticleId + 1;
        endArticleId = endArticleId + 10;
    }

    function reduce()
    {
        endArticleId = endArticleId - 10;
        startArticleId = endArticleId - 9;
    }

    async function nextPage()
    {
        add();
        let response = await fetch("http://wiki/articles/" + startArticleId + "/" + endArticleId);
        let body = await response.json();
        if (body.length === 0) {
            reduce();
        } else {
            articles.value = body;
        }
    }

    async function lastPage()
    {
        reduce();
        if (startArticleId <= 0) {
            add();
        }
        let response = await fetch("http://wiki/articles/" + startArticleId + "/" + endArticleId);
        articles.value = await response.json();
    }

    function changeVisibility()
    {
        importShow.value = !importShow.value;
        searchShow.value = !searchShow.value;
    }

    function showContent(articleContent)
    {
        contentShow.value = !contentShow.value;
        content.value = articleContent;
    }

    function dontShowContent()
    {
        contentShow.value = false;
    }

    async function send(e)
    {
        e.preventDefault();
        errorShow.value = false;
        let response = await fetch("http://wiki/import", {
            method: 'POST',
            body: new FormData(e.currentTarget)
        });
        let body = await response.json();
        if (body.hasOwnProperty("message")) {
            if (body.message === "already copied" || body.message === "not found") {
                errorShow.value = true;
                error.value = (body.message === "already copied") ? "Статья уже скопирована." : ((body.message === "not found") ? "Статья не найдена на wikipedia." : "");
            }
        }
        article.value = body;
        if (article.value.hasOwnProperty("title")) {
            articleShow.value = true;
            if (Object.keys(articles.value).length === 0 || endArticleId <= 10) {
                await init();
            }
        }
    }

    async function find(e)
    {
        e.preventDefault();
        let response = await fetch("http://wiki/search", {
            method: 'POST',
            body: new FormData(e.currentTarget)
        });
        searchResult.value = await response.json();
        if (Object.keys(searchResult).length !== 0) {
            searchResultShow.value = true;
        }
    }
</script>

<template>
    <button @click="changeVisibility">Import</button>
    <button @click="changeVisibility">Search</button>
    <div v-if="importShow">
        <form action="/import" method="post" @submit="send">
            <input type="hidden" name="_token" :value="props.csrfToken">
            <input type="text" name="title" placeholder="ключевое слово" required>
            <button type="submit">Скопировать</button>
            <button type="reset">Очистить</button>
        </form>
        <p v-if="errorShow">{{ error }}</p>
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
            <button @click="lastPage">Предыдущая страница</button>
            <button @click="nextPage">Следующая страница</button>
        </div>
    </div>
    <div v-if="searchShow">
        <form action="/search" method="post" @submit="find">
            <input type="hidden" name="_token" :value="props.csrfToken">
            <input type="text" name="keyword" placeholder="ключевое слово" required>
            <button type="submit">Найти</button>
        </form>
        <div v-if="searchResultShow">
            <p>Найдено статей: {{ searchResult.length }}</p>
            <ul v-for="article in searchResult">
                <li @click="showContent(article.content)">{{ article.title + " (кол-во вхождений: " + article.numberOfOccurrences + ")"}}</li>
            </ul>
            <pre>
                <article v-if="contentShow" @click="dontShowContent" class="content">{{ content }}</article>
            </pre>
        </div>
    </div>
</template>

<style scoped>
    .content {
        position: fixed;
        box-sizing: border-box;
        width: 1250px;
        height: 800px;
        left: 600px;
        bottom: 50px;
        overflow: scroll;
    }
</style>
