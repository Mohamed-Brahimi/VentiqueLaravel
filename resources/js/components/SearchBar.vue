<template>
    <div id="antique-search-container">
        <form @submit.prevent="handleSearch">
            <div class="form-group">
                <input 
                    type="text" 
                    v-model="searchQuery"
                    @input="onSearchInput"
                    @focus="onFocus"
                    @blur="hideSuggestions"
                    @keydown.down.prevent="navigateDown"
                    @keydown.up.prevent="navigateUp"
                    @keydown.enter.prevent="selectHighlighted"
                    class="antique-searchbar form-control" 
                    id="antique_search" 
                    placeholder="Rechercher une antiquité..."
                    autocomplete="off"
                />
                <ul 
                    v-if="showSuggestions && suggestions.length > 0" 
                    class="autocomplete-results"
                    role="listbox"
                >
                    <li 
                        v-for="(antique, index) in suggestions" 
                        :key="antique.id"
                        @mousedown.prevent="selectAntique(antique)"
                        @mouseenter="highlightedIndex = index"
                        :class="['autocomplete-item', { 'highlighted': index === highlightedIndex }]"
                        role="option"
                        :aria-selected="index === highlightedIndex"
                    >
                        <span class="antique-name">{{ antique.name }}</span>
                        <span v-if="antique.price" class="antique-price">
                            {{ formatPrice(antique.price) }}
                        </span>
                    </li>
                </ul>
                <div v-if="showSuggestions && searchQuery.trim().length >= 2 && suggestions.length === 0" class="no-results">
                    Aucun résultat trouvé
                </div>
                <div v-if="loading" class="autocomplete-loading">
                    Recherche...
                </div>
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
const highlightedIndex = ref(-1);
const loading = ref(false);
let debounceTimeout = null;

const onSearchInput = () => {
    if (debounceTimeout) {
        clearTimeout(debounceTimeout);
    }

    if (!searchQuery.value || searchQuery.value.trim().length < 2) {
        suggestions.value = [];
        showSuggestions.value = false;
        loading.value = false;
        return;
    }

    loading.value = true;

    debounceTimeout = setTimeout(async () => {
        try {
            const response = await api.post('/autocomplete', {
                search: searchQuery.value
            });
            
            console.log('Autocomplete response:', response.data);
            
            if (response.data && Array.isArray(response.data)) {
                suggestions.value = response.data;
                showSuggestions.value = true;
                highlightedIndex.value = -1;
            }
        } catch (error) {
            console.error('Autocomplete error:', error);
            suggestions.value = [];
        } finally {
            loading.value = false;
        }
    }, 300);
};

const onFocus = () => {
    if (searchQuery.value.trim().length >= 2 && suggestions.value.length > 0) {
        showSuggestions.value = true;
    }
};

const navigateDown = () => {
    if (!showSuggestions.value || suggestions.value.length === 0) return;
    
    highlightedIndex.value = (highlightedIndex.value + 1) % suggestions.value.length;
};

const navigateUp = () => {
    if (!showSuggestions.value || suggestions.value.length === 0) return;
    
    if (highlightedIndex.value <= 0) {
        highlightedIndex.value = suggestions.value.length - 1;
    } else {
        highlightedIndex.value--;
    }
};

const selectHighlighted = () => {
    if (highlightedIndex.value >= 0 && highlightedIndex.value < suggestions.value.length) {
        selectAntique(suggestions.value[highlightedIndex.value]);
    } else {
        handleSearch();
    }
};

const selectAntique = (antique) => {
    searchQuery.value = antique.name;
    suggestions.value = [];
    showSuggestions.value = false;
    highlightedIndex.value = -1;
    handleSearch();
};

const hideSuggestions = () => {
    setTimeout(() => {
        showSuggestions.value = false;
        highlightedIndex.value = -1;
    }, 200);
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ path: '/', query: { search: searchQuery.value } });
        suggestions.value = [];
        showSuggestions.value = false;
        highlightedIndex.value = -1;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-CA', {
        style: 'currency',
        currency: 'CAD'
    }).format(price);
};
</script>

<style scoped>
#antique-search-container {
    flex: 1;
    max-width: 400px;
    margin: 0 20px;
    position: relative;
}

.form-group {
    margin: 0;
    position: relative;
}

.antique-searchbar {
    width: 100%;
    padding: 12px 20px;
    border: 2px solid #edc58a;
    border-radius: 25px;
    font-size: 15px;
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
    overflow-x: hidden;

    z-index: 1000;
    box-shadow: 0 4px 8px rgba(46, 29, 25, 0.2);
}

.autocomplete-item {
    padding: 12px 20px;
    cursor: pointer;
    color: #2e1d19;
    font-weight: 600;
    border-bottom: 1px solid #edc58a;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.autocomplete-item:last-child {
    border-bottom: none;
    border-radius: 0 0 10px 10px;
}

.autocomplete-item:hover,
.autocomplete-item.highlighted {
    background-color: #edc58a;
    color: #2e1d19;
    transform: translateX(2px);
}

.antique-name {
    flex: 1;
    font-size: 14px;
}

.antique-price {
    font-size: 13px;
    color: #725042;
    font-weight: 700;
    margin-left: 10px;
}

.autocomplete-item.highlighted .antique-price {
    color: #2e1d19;
}

.no-results {
    position: absolute;
    top: calc(100% - 2px);
    left: 0;
    right: 0;
    background-color: #f9e0bb;
    border: 2px solid #725042;
    border-top: none;
    border-radius: 0 0 12px 12px;
    padding: 16px 20px;
    text-align: center;
    color: #725042;
    font-style: italic;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(46, 29, 25, 0.2);
}

.autocomplete-loading {
    position: absolute;
    top: calc(100% - 2px);
    left: 0;
    right: 0;
    background-color: #f9e0bb;
    border: 2px solid #725042;
    border-top: none;
    border-radius: 0 0 12px 12px;
    padding: 12px 20px;
    text-align: center;
    color: #725042;
    font-style: italic;
    z-index: 1000;
    font-size: 14px;
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
    transition: background 0.2s;
}

.autocomplete-results::-webkit-scrollbar-thumb:hover {
    background: #2e1d19;
}

@media (max-width: 768px) {
    #antique-search-container {
        max-width: 100%;
        margin: 15px 0;
    }
    
    .autocomplete-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 16px;
    }
    
    .antique-price {
        margin-left: 0;
        margin-top: 4px;
        font-size: 12px;
    }
}
</style>