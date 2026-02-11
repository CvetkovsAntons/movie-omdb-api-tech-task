<template>
    <b-alert v-if="error" variant="danger" show>{{ error }}</b-alert>

    <b-spinner v-else-if="loading"/>

    <b-list-group v-else-if="movies.length">
        <b-list-group-item v-for="movie in movies" :key="movie.imdbID" class="d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ movie.Title }}</strong>
                <div class="text-muted small">
                    {{ movie.Year }} · {{ movie.Type }}
                </div>
            </div>

            <b-button size="sm" variant="outline-primary" @click="$emit('select', movie.imdbID)">
                View
            </b-button>
        </b-list-group-item>
    </b-list-group>

  <p v-else>
    Search result is empty...
  </p>
</template>

<script setup>
import { BAlert, BButton, BListGroup, BListGroupItem, BSpinner } from "bootstrap-vue-next";

defineProps({ movies: Array, loading: Boolean, error: String });

defineEmits(["select"]);
</script>