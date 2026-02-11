<template>
    <div class="page">
        <h1>Movie search</h1>

        <SearchBar :loading="loading" :recent="recentSearches" @search="onSearch" />

        <MovieList :movies="movies" :loading="loading" :error="error" @select="openMovie"/>

        <MovieModal :open="modalOpen" :movieID="selectedMovieID" @close="closeMovie"/>
    </div>
</template>

<script setup>
import {ref, onMounted} from "vue";
import SearchBar from "./components/SearchBar.vue";
import MovieList from "./components/MovieList.vue";
import MovieModal from "./components/MovieModal.vue";

const loading = ref(false);
const error = ref(null);
const movies = ref([]);

const modalOpen = ref(false);
const selectedMovieID = ref(null);

const RECENT_SEARCHES_KEY = "movies.recentSearches";
const recentSearches = ref([]);

function loadRecentSearches() {
    try {
        const storedSearches = localStorage.getItem(RECENT_SEARCHES_KEY);
        recentSearches.value = storedSearches ? JSON.parse(storedSearches) : [];
    } catch {
        recentSearches.value = [];
    }
}

function pushRecentSearch(searchQuery) {
    if (searchQuery.length < 2) {
        return;
    }

    recentSearches.value = [searchQuery, ...recentSearches.value.filter((x) => x !== searchQuery)].slice(0, 5);

    localStorage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(recentSearches.value));
}

async function onSearch(searchQuery) {
    if (!searchQuery) {
        return;
    }

    searchQuery = searchQuery.trim();

    if (searchQuery.length < 2) {
        error.value = "Query must be at least 2 characters";
        return;
    }

    pushRecentSearch(searchQuery);

    error.value = null;
    movies.value = [];

    loading.value = true;

    try {
        const response = await fetch(
            `/api/movies/search?s=${encodeURIComponent(searchQuery)}`,
            {headers: {Accept: "application/json"}}
        );

        if (!response.ok) {
            throw new Error(response?.message ?? `HTTP ${response.status}`);
        }

        const results = (await response.json())?.results;

        movies.value = Array.isArray(results) ? results : [];
    } catch (err) {
        error.value = err?.message ?? "Search failed";
    } finally {
        loading.value = false;
    }
}

function openMovie(movieID) {
    selectedMovieID.value = movieID;
    modalOpen.value = true;
}

function closeMovie() {
    modalOpen.value = false;
    selectedMovieID.value = null;
}

onMounted(loadRecentSearches);
</script>

<style scoped>
.page {
    max-width: 900px;
    margin: 0 auto;
    padding: 24px;
    font-family: system-ui, sans-serif;
}
</style>