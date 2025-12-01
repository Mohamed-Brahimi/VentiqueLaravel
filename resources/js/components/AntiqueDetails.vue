<template>
    <Teleport to="body">
        <div v-if="show" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content details-modal">
                <button class="close-btn" @click="closeModal" type="button">×</button>
                
                <div v-if="loading" class="loading">Chargement...</div>
                
                <div v-else-if="error" class="error">{{ error }}</div>
                
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
                            <h2 class="antique-title">{{ antique.name }}</h2>
                            
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

                            <div class="action-buttons" v-if="isOwner">
                                <button @click="openEdit" class="btn btn-secondary">
                                    Modifier
                                </button>
                                <button @click="deleteAntique" class="btn btn-danger">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <EditAntique 
                    :show="showEditModal" 
                    :antiqueId="antique?.id"
                    @close="showEditModal = false"
                    @success="handleEditSuccess"
                />
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import api from '../axios';
import EditAntique from './EditAntique.vue';

const props = defineProps({
    show: { type: Boolean, required: true },
    antiqueId: { type: [Number, String], required: false }
});

const emit = defineEmits(['close', 'refresh']);

const antique = ref(null);
const loading = ref(false);
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
    } else {
        const userDataString = localStorage.getItem('user');
        if (userDataString) {
            try {
                const userData = JSON.parse(userDataString);
                userId = userData.id;
            } catch (e) {}
        }
    }
    
    return userId && antique.value.user_id === userId;
});

watch(() => [props.show, props.antiqueId], ([showValue, id]) => {
    if (showValue && id) {
        fetchAntiqueDetails(id);
    }
}, { immediate: true });

async function fetchAntiqueDetails(id) {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await api.get(`/api/antiques/${id}`);
        antique.value = response.data;
    } catch (err) {
        error.value = 'Erreur lors du chargement des détails';
    } finally {
        loading.value = false;
    }
}

function closeModal() {
    emit('close');
}

function openEdit() {
    showEditModal.value = true;
}

async function deleteAntique() {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette antiquité ?')) {
        return;
    }
    
    try {
        await api.delete(`/api/antiques/${antique.value.id}`);
        alert('Antiquité supprimée avec succès');
        emit('refresh');
        closeModal();
    } catch (err) {
        alert('Erreur lors de la suppression');
    }
}

function handleEditSuccess() {
    showEditModal.value = false;
    fetchAntiqueDetails(props.antiqueId);
    emit('refresh');
}

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
    event.target.src = '/images/default-antique.jpg';
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    padding: 2rem;
    overflow-y: auto;
}

.modal-content.details-modal {
    background-color: #fff;
    border: 3px solid #2e1d19;
    border-radius: 12px;
    max-width: 1200px;
    width: 100%;
    max-height: 95vh;
    overflow-y: auto;
    position: relative;
}

.close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #2e1d19;
    color: #edc58a;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 28px;
    cursor: pointer;
    z-index: 10;
}

.close-btn:hover {
    background-color: #725042;
    transform: rotate(90deg);
}

.loading, .error {
    text-align: center;
    padding: 3rem;
    font-size: 1.2rem;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}

.image-section {
    background-color: #edc58a;
    padding: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.antique-image-large {
    width: 100%;
    max-height: 500px;
    object-fit: contain;
    border-radius: 8px;
}

.info-section {
    padding: 2rem;
    background-color: #f9e0bb;
}

.antique-title {
    color: #2e1d19;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.antique-price {
    font-size: 1.8rem;
    color: #2e1d19;
    font-weight: bold;
}

.antique-seller, .antique-date {
    color: #725042;
    margin: 0.5rem 0;
}

.antique-description-full h3 {
    color: #2e1d19;
    margin-top: 1.5rem;
}

.antique-description-full p {
    color: #725042;
    line-height: 1.6;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: 2px solid;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-secondary {
    background-color: #725042;
    border-color: #725042;
    color: #f9e0bb;
}

.btn-secondary:hover {
    background-color: #2e1d19;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

@media (max-width: 968px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}
</style>