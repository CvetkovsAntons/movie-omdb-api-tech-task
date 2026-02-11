<template>
    <b-modal v-model="openInternal" title="Movie details" size="lg" hide-footer>
        <b-spinner v-if="loading"/>

        <b-alert v-else-if="error" variant="danger" show>{{ error }}</b-alert>

        <div v-else-if="movie">
            <b-row>
                <b-col cols="4">
                    <b-img v-if="movie.Poster !== 'N/A'" :src="movie.Poster" fluid rounded />
                </b-col>

                <b-col>
                    <h4>{{ movie.Title }} ({{ movie.Year }})</h4>
                    <p class="text-muted">{{ movie.Genre }}</p>
                    <p><strong>IMDb:</strong> {{ movie.imdbRating }}</p>
                    <p>{{ movie.Plot }}</p>
                </b-col>
            </b-row>
        </div>
    </b-modal>
</template>

<script setup>
import {ref, watch} from "vue";
import { BAlert, BCol, BImg, BModal, BRow, BSpinner } from "bootstrap-vue-next";

const props = defineProps({ open: Boolean, movieID: String });

const emit = defineEmits(["close"]);

const openInternal = ref(false);
const loading = ref(false);
const error = ref(null);
const movie = ref(null);

async function loadMovieData(movieID) {
    if (!movieID) {
        return;
    }

    loading.value = true;

    error.value = null;

    try {
        const response = await fetch(
            `/api/movie/${movieID}`,
            {headers: {Accept: "application/json"}},
        );

        movie.value = await response.json();
    } catch {
        error.value = "Failed to load movie";
    } finally {
        loading.value = false;
    }
}

watch(() => props.open, (v) => (openInternal.value = v));

watch(() => props.movieID, (id) => loadMovieData(id));

watch(openInternal, (v) => { if (!v) emit("close"); });
</script>