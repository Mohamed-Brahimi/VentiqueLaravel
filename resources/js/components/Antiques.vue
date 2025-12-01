<template>
    <div class="antiques-container">
        <h2 class="antiques-title">Liste des Antiquités</h2>
        
        <div v-if="loading" class="loading">
            Chargement...
        </div>
        
        <div v-else-if="error" class="error">
            {{ error }}
        </div>
        
        <div v-else-if="antiques.length === 0" class="no-results">
            Aucune antiquité trouvée
        </div>
        
        <div v-else class="antiques-grid">
            <div 
                v-for="antique in antiques" 
                :key="antique.id" 
                class="antique-card"
            >
                <div class="antique-image-container">
                    <img 
                        :src="antique.image_url || antique.image || '/images/default-antique.jpg'" 
                        :alt="antique.name"
                        class="antique-image"
                        @error="handleImageError"
                    />
                </div>
                
                <div class="antique-content">
                    <h3 class="antique-name">{{ antique.name }}</h3>
                    <p class="antique-description">{{ truncateDescription(antique.description) }}</p>
                    <p class="antique-price">{{ formatPrice(antique.price) }}</p>
                    <p class="antique-seller" v-if="antique.user">
                        Vendeur: {{ antique.user.name }}
                    </p>
                    
                    <button 
                        @click="viewDetails(antique.id)" 
                        class="btn-details"
                    >
                        Voir détails
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import api from '../axios';

const route = useRoute();
const emit = defineEmits(['show-antique-details']);
const antiques = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchAntiques = async (searchQuery = '') => {
    loading.value = true;
    error.value = null;
    
    try {
        let url = '/api/antiques';
        if (searchQuery) {
            url += `?search=${encodeURIComponent(searchQuery)}`;
        }
        
        console.log('Fetching antiques from:', url);
        
        const response = await api.get(url);
        antiques.value = response.data.data || response.data;
        
        console.log('Fetched antiques:', antiques.value.length);
    } catch (err) {
        console.error('Error fetching antiques:', err);
        error.value = 'Erreur lors du chargement des antiquités';
    } finally {
        loading.value = false;
    }
};

const truncateDescription = (description) => {
    if (!description) return '';
    return description.length > 100 
        ? description.substring(0, 100) + '...' 
        : description;
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-CA', {
        style: 'currency',
        currency: 'CAD'
    }).format(price);
};

const handleImageError = (event) => {
    event.target.src = '/images/default-antique.jpg';
};

const viewDetails = (id) => {
    emit('show-antique-details', id);
};

// Watch for search query changes in URL
watch(() => route.query.search, (newSearch) => {
    console.log('Search query changed:', newSearch);
    fetchAntiques(newSearch);
}, { immediate: false });

onMounted(() => {
    fetchAntiques(route.query.search);
});
</script>

<style scoped>
.antiques-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    background-color: #f9e0bb;
    min-height: 100vh;
}

.antiques-title {
    text-align: center;
    color: #2e1d19;
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 2rem;
    font-family: 'Georgia', serif;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.loading, .error, .no-results {
    text-align: center;
    padding: 3rem;
    font-size: 1.2rem;
    color: #2e1d19;
    font-weight: bold;
}

.error {
    color: #dc3545;
}

.antiques-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1rem;
}

.antique-card {
    background-color: #fff;
    border: 3px solid #2e1d19;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(46, 29, 25, 0.1);
    display: flex;
    flex-direction: column;
}

.antique-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(46, 29, 25, 0.2);
    border-color: #725042;
}

.antique-image-container {
    width: 100%;
    height: 250px;
    overflow: hidden;
    background-color: #edc58a;
    border-bottom: 3px solid #2e1d19;
}

.antique-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.antique-card:hover .antique-image {
    transform: scale(1.05);
}

.antique-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    background-color: #f9e0bb;
}

.antique-name {
    color: #2e1d19;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 0 1rem 0;
    font-family: 'Georgia', serif;
}

.antique-description {
    color: #725042;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1rem;
    flex-grow: 1;
}

.antique-price {
    color: #2e1d19;
    font-size: 1.3rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.antique-seller {
    color: #725042;
    font-size: 0.9rem;
    font-style: italic;
    margin: 0.5rem 0 1rem 0;
}

.btn-details {
    background-color: #2e1d19;
    color: #edc58a;
    border: 2px solid #2e1d19;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 1px;
}

.btn-details:hover {
    background-color: #725042;
    border-color: #725042;
    color: #f9e0bb;
    transform: translateY(-2px);
}

.btn-add {
    background-color: #2e1d19;
    color: #edc58a;
    border: 2px solid #2e1d19;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 1px;
    margin: 2rem 0;
    display: block;
    width: 100%;
    max-width: 300px;
    margin-left: auto;
    margin-right: auto;
}

.btn-add:hover {
    background-color: #725042;
    border-color: #725042;
    color: #f9e0bb;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .antiques-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .antiques-title {
        font-size: 2rem;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .antiques-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
