<template>
        <Head>
            <title>Поиск фильмов MovieOne.ru</title>
            <meta name="description"
                  content="Поиск фильмов, мультиков, аниме и документальных видео"/>
            <meta name="keywords" content="Поиск фильмов, мультиков, аниме и документальных видео"/>
        </Head>
    <section class="px-4 h-[70px] grid grid-flow-col justify-items-start bg-white content-center">
        <h1 class="text-gray-900">Результаты запроса: {{ data.key }}</h1>
    </section>

        <section class="container mx-auto my-4
    grid grid-flow-row gap-4 z-20 bg-slate-100
    ">
          <movies-card :data="movies" :config="{classes: 'col-xxl-2 col-xl-3 col-md-4 mb-4'}"></movies-card>
        </section>

</template>


<script>
import {Head, Link} from "@inertiajs/vue3";
import FrontLayout from "@/Layouts/FrontLayout.vue";
import MoviesCard from "@/Components/MoviesCard.vue";


export default {
    name: "Catalog",
    layout: FrontLayout,
    props: ['movies', 'data'],
    components: {Head, Link, MoviesCard},
    mounted() {
        this.$parent.searchKey = ''
        ym(94438576, 'hit', '/search');
    },

}
</script>



