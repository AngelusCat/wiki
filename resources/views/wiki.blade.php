@vite(['resources/js/app.js'])

<div id="app">
    <wiki :csrf-token='@json($csrfToken)'></wiki>
</div>
