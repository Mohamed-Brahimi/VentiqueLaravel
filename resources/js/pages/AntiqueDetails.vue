<template>
    <div class="details-container">
        <div v-if="loading" class="loading">
            Chargement...
        </div>
        
        
        
        <div v-else-if="antique" class="antique-details">
            <div class="details-grid">
                <div class="image-section">
                    <img 
                        :src="antique.image_url || antique.image" 
                        :alt="antique.name"
                        class="antique-image-large"
                        @error="handleImageError"
                    />
                </div>

                <div class="info-section">
                    <h1 class="antique-title">{{ antique.name }}</h1>
                    
                    <div class="antique-meta">
                        <p class="antique-price">{{ formatPrice(antique.price) }}</p>
                        <p class="antique-seller" v-if="antique.user">
                            <strong>Vendeur:</strong> {{ antique.user.name }}
                        </p>
                        <p class="antique-date">
                            <strong>Publié le:</strong> {{ formatDate(antique.created_at) }}
                        </p>
                    </div>

                    <div class="antique-description-full">
                        <h3>Description</h3>
                        <p>{{ antique.description }}</p>
                    </div>

                    <div class="action-buttons">
                        <button 
                            v-if="isOwner"
                            @click="showEditModal = true" 
                            class="btn btn-secondary"
                        >
                            Modifier
                        </button>
                        
                        <button 
                            v-if="isOwner"
                            @click="deleteAntique" 
                            class="btn btn-danger"
                        >
                            Supprimer
                        </button>
                        
                    
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Edit Modal -->
        <EditAntique 
            :show="showEditModal" 
            :antiqueId="antique?.id"
            @close="showEditModal = false"
            @success="handleEditSuccess"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../axios';
import EditAntique from '../components/EditAntique.vue';

const route = useRoute();
const router = useRouter();

const antique = ref(null);
const loading = ref(true);
const error = ref(null);
const showEditModal = ref(false);

const isLoggedIn = computed(() => {
    return window.Laravel?.isLoggedIn || localStorage.getItem('token');
});

const isOwner = computed(() => {
    if (!antique.value || !isLoggedIn.value) return false;
    
    let userId = null;
    
    if (window.Laravel?.user?.id) {
        userId = window.Laravel.user.id;
    } 
    else {
        const userDataString = localStorage.getItem('user');
        if (userDataString) {
            try {
                const userData = JSON.parse(userDataString);
                if (!userData.id) {
                    fetchCurrentUser();
                } else {
                    userId = userData.id;
                }
            } catch (e) {
                console.error('Error parsing user data:', e);
            }
        }
    }
    
    console.log('Checking ownership - User ID:', userId, 'Antique User ID:', antique.value.user_id);
    return userId && antique.value.user_id === userId;
});

const fetchCurrentUser = async () => {
    try {
        const response = await api.get('/api/user');
        if (response.data) {
            // Update localStorage with full user data including ID
            localStorage.setItem('user', JSON.stringify(response.data));
            
            // Update window.Laravel
            if (window.Laravel) {
                window.Laravel.user = response.data;
            }
        }
    } catch (error) {
        console.error('Error fetching current user:', error);
    }
};

const fetchAntiqueDetails = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const id = route.params.id;
        console.log('Fetching antique details for ID:', id);
        
        const response = await api.get(`/api/antiques/${id}`);
        console.log('Antique details response:', response.data);
        
        antique.value = response.data;
        
    } catch (err) {
        console.error('Error fetching antique details:', err);
        error.value = err.response?.status === 404 
            ? 'Antiquité non trouvée' 
            : 'Erreur lors du chargement des détails';
    } finally {
        loading.value = false;
    }
};

const handleEditSuccess = () => {
    showEditModal.value = false;
    // Refresh the antique details
    fetchAntiqueDetails();
};

const deleteAntique = async () => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette antiquité ?')) {
        return;
    }
    
    try {
        await api.delete(`/api/antiques/${antique.value.id}`);
        alert('Antiquité supprimée avec succès');
        router.push('/');
    } catch (err) {
        console.error('Error deleting antique:', err);
        alert('Erreur lors de la suppression');
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-CA', {
        style: 'currency',
        currency: 'CAD'
    }).format(price);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-CA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const handleImageError = (event) => {
    console.error('Image failed to load:', event.target.src);
    event.target.src = '/images/default-antique.jpg';
};

onMounted(() => {
    fetchAntiqueDetails();
    // Fetch current user if logged in but user data is incomplete
    if (isLoggedIn.value) {
        fetchCurrentUser();
    }
});
</script>

<style scoped>
.details-container {
    padding: 2rem;
    background-color: #f9e0bb;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.loading, .error {
    text-align: center;
    padding: 3rem;
    font-size: 1.2rem;
    color: #2e1d19;
    font-weight: bold;
}

.error {
    color: #dc3545;
}

.antique-details {
    background-color: #fff;
    border: 3px solid #2e1d19;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 16px rgba(46, 29, 25, 0.15);
    max-width: 1400px;
    width: 100%;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}

.image-section {
    background-color: #edc58a;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.antique-image-large {
    width: 100%;
    max-height: 600px;
    object-fit: contain;
    border-radius: 8px;
}

.info-section {
    padding: 3rem;
    background-color: #f9e0bb;
}

.antique-title {
    font-size: 2.5rem;
    color: #2e1d19;
    font-weight: bold;
    margin: 0 0 1.5rem 0;
    font-family: 'Georgia', serif;
    border-bottom: 3px solid #2e1d19;
    padding-bottom: 1rem;
}

.antique-meta {
    margin-bottom: 2rem;
}

.antique-price {
    font-size: 2rem;
    color: #2e1d19;
    font-weight: bold;
    margin: 1rem 0;
}

.antique-seller,
.antique-date {
    font-size: 1rem;
    color: #725042;
    margin: 0.5rem 0;
}

.antique-description-full {
    margin: 2rem 0;
}

.antique-description-full h3 {
    color: #2e1d19;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.antique-description-full p {
    color: #725042;
    font-size: 1.1rem;
    line-height: 1.8;
    white-space: pre-wrap;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin: 2rem 0;
    flex-wrap: wrap;
    align-items: center;
}

.btn {
    padding: 1rem 2rem;
    border: 2px solid;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 1rem;
}

.btn-primary {
    background-color: #2e1d19;
    border-color: #2e1d19;
    color: #edc58a;
}

.btn-primary:hover {
    background-color: #725042;
    border-color: #725042;
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: #725042;
    border-color: #725042;
    color: #f9e0bb;
}

.btn-secondary:hover {
    background-color: #2e1d19;
    transform: translateY(-2px);
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.btn-back {
    background-color: #725042;
    color: #f9e0bb;
    padding: 0.75rem 1.5rem;
    text-decoration: none;
    border-radius: 8px;
    display: inline-block;
    margin-top: 1rem;
}

.login-message {
    color: #725042;
    font-style: italic;
    margin: 0;
}

.login-message a {
    color: #2e1d19;
    font-weight: bold;
    text-decoration: underline;
}

.offers-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 3px solid #2e1d19;
}

.offers-section h3 {
    color: #2e1d19;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.offers-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.offer-item {
    background-color: #fff;
    border: 2px solid #edc58a;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.offer-amount {
    font-weight: bold;
    color: #2e1d19;
    font-size: 1.2rem;
}

.offer-user {
    color: #725042;
}

.offer-date {
    color: #725042;
    font-size: 0.9rem;
    font-style: italic;
}

@media (max-width: 968px) {
    .details-container {
        padding: 1rem;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .antique-title {
        font-size: 2rem;
    }
    
    .info-section {
        padding: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .offer-item {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>