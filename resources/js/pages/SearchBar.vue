<template>
    <div id="antique-search-container">
        <form @submit.prevent="handleSearch">
            <div class="form-group">
                <input 
                    type="text" 
                    v-model="searchQuery"
                    @input="onSearchInput"
                    @focus="showSuggestions = true"
                    @blur="hideSuggestions"
                    class="antique-searchbar form-control" 
                    id="antique_search" 
                    placeholder="Rechercher une antiquité..."
                    autocomplete="off"
                />
                <ul v-if="showSuggestions && suggestions.length > 0" class="autocomplete-results">
                    <li 
                        v-for="antique in suggestions" 
                        :key="antique.id"
                        @mousedown.prevent="selectAntique(antique)"
                        class="autocomplete-item"
                    >
                        {{ antique.name }}
                    </li>
                </ul>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const searchQuery = ref('');
const suggestions = ref([]);
const showSuggestions = ref(false);
let debounceTimeout = null;

const onSearchInput = () => {
    if (debounceTimeout) {
        clearTimeout(debounceTimeout);
    }

    if (!searchQuery.value || searchQuery.value.trim().length < 2) {
        suggestions.value = [];
        return;
    }

    debounceTimeout = setTimeout(async () => {
        try {
            const response = await api.post('/autocomplete', {
                search: searchQuery.value
            });
            
            console.log('Autocomplete response:', response.data);
            
            if (response.data && Array.isArray(response.data)) {
                suggestions.value = response.data;
                showSuggestions.value = true;
            }
        } catch (error) {
            console.error('Autocomplete error:', error);
            suggestions.value = [];
        }
    }, 300);
};

const selectAntique = (antique) => {
    searchQuery.value = antique.name;
    suggestions.value = [];
    showSuggestions.value = false;
    handleSearch();
};

const hideSuggestions = () => {
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ path: '/', query: { search: searchQuery.value } });
        suggestions.value = [];
        showSuggestions.value = false;
    }
};
</script>

<style scoped>
#antique-search-container {
    flex: 1;
    max-width: 350px;
    margin: 0 20px;
    position: relative;
}

.form-group {
    margin: 0;
    position: relative;
}

.antique-searchbar {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid #edc58a;
    border-radius: 25px;
    font-size: 14px;
    background-color: #f9e0bb;
    color: #2e1d19;
    outline: none;
    transition: all 0.3s ease;
    font-weight: 600;
    text-align: center;
}

.antique-searchbar::placeholder {
    color: #725042;
    opacity: 0.7;
    font-weight: 500;
}

.antique-searchbar:focus {
    border-color: #725042;
    box-shadow: 0 0 0 3px rgba(237, 197, 138, 0.3);
    text-align: left;
}

.autocomplete-results {
    position: absolute;
    top: calc(100% - 2px);
    left: 0;
    right: 0;
    background-color: #f9e0bb;
    border: 2px solid #725042;
    border-top: none;
    border-radius: 0 0 12px 12px;
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(46, 29, 25, 0.2);
}

.autocomplete-item {
    padding: 12px 16px;
    cursor: pointer;
    color: #2e1d19;
    font-weight: 600;
    border-bottom: 1px solid #edc58a;
    transition: background-color 0.2s ease;
}

.autocomplete-item:last-child {
    border-bottom: none;
    border-radius: 0 0 10px 10px;
}

.autocomplete-item:hover {
    background-color: #edc58a;
    color: #2e1d19;
}

.autocomplete-results::-webkit-scrollbar {
    width: 8px;
}

.autocomplete-results::-webkit-scrollbar-track {
    background: #f9e0bb;
    border-radius: 0 0 10px 0;
}

.autocomplete-results::-webkit-scrollbar-thumb {
    background: #725042;
    border-radius: 4px;
}

.autocomplete-results::-webkit-scrollbar-thumb:hover {
    background: #2e1d19;
}

@media (max-width: 768px) {
    #antique-search-container {
        max-width: 100%;
        margin: 15px 0;
    }
}
</style>