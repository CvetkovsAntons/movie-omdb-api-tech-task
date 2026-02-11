<template>
    <b-card class="mb-3 position-relative">
        <b-form @submit.prevent="submit">
            <b-input-group>
                <b-form-input ref="inputSearchBox" v-model="searchQuery" placeholder="Search movie..." :disabled="loading"
                    autocomplete="off" @focus="showRecentSearches = true" @keydown.enter.prevent="submit" />

                <b-button type="submit" variant="primary" :disabled="loading || searchQuery.trim().length < 2">
                    Search
                </b-button>
            </b-input-group>
        </b-form>

        <ul v-if="showRecentSearches && filteredRecent.length" class="recent-dropdown list-group">
            <li v-for="item in filteredRecent" :key="item" class="list-group-item list-group-item-action" @mousedown.prevent="select(item)">
                {{ item }}
            </li>
        </ul>
    </b-card>
</template>

<script setup>
import { BButton, BCard, BForm, BFormInput, BInputGroup } from "bootstrap-vue-next";
import { computed, ref, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({ loading: Boolean, recent: { type: Array, default: () => [] } });

const emit = defineEmits(["search"]);

const searchQuery = ref("");
const showRecentSearches = ref(false);
const inputSearchBox = ref(null);

const filteredRecent = computed(() => {
    if (!searchQuery.value) {
        return props.recent;
    }

    return props.recent.filter((x) => x.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

function submit() {
    showRecentSearches.value = false;
    emit("search", searchQuery.value);
}

function select(value) {
    searchQuery.value = value;
    submit();
}

function handleClickOutside(e) {
    if (!inputSearchBox.value?.$el?.contains(e.target)) {
        showRecentSearches.value = false;
    }
}

onMounted(() => document.addEventListener("click", handleClickOutside));
onBeforeUnmount(() => document.removeEventListener("click", handleClickOutside));
</script>

<style scoped>
.recent-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
}
</style>