<template>
    <Teleport to="body">
        <div v-if="show" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <button class="close-btn" @click="closeModal" type="button">×</button>
                
                <h2 class="form-title">Modifier une Antiquité</h2>

                <div v-if="error" class="alert alert-danger">
                    {{ error }}
                </div>

                <form @submit.prevent="editAntique" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name" class="form-label">Nom de l'antiquité</label>
                        <input 
                            type="text" 
                            id="name"
                            v-model="antique.name" 
                            class="form-control" 
                            placeholder="Entrez le nom"
                            required 
                        />
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea 
                            id="description"
                            v-model="antique.description" 
                            class="form-control" 
                            placeholder="Décrivez votre antiquité..."
                            rows="6"
                            required
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price" class="form-label">Prix minimum ($)</label>
                        <input 
                            type="number" 
                            id="price"
                            v-model="antique.price" 
                            class="form-control" 
                            placeholder="0.00"
                            min="0.01"
                            step="0.01"
                            required 
                        />
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Image</label>
                        
                        <div 
                            class="dropzone"
                            @drop.prevent="handleDrop"
                            @dragover.prevent
                            @dragenter.prevent="dragActive = true"
                            @dragleave.prevent="dragActive = false"
                            @click="$refs.fileInput.click()"
                            :class="{ 'drag-active': dragActive }"
                        >
                            <p v-if="!imagePreview">Glissez-déposez une image ici ou cliquez pour sélectionner</p>
                            <img v-else :src="imagePreview" alt="Aperçu" class="preview-image" />
                            <input 
                                type="file" 
                                ref="fileInput"
                                @change="handleFileSelect"
                                accept="image/*"
                                hidden
                            />
                        </div>

                        <small class="form-text">Formats acceptés: JPEG, PNG, JPG, GIF (max 5MB)</small>
                    </div>

                    <div class="form-buttons">
                        <button 
                            type="submit" 
                            class="btn btn-primary" 
                            :disabled="loading"
                        >
                            {{ loading ? "Envoi en cours..." : "Modifier l'antiquité" }}
                        </button>
                        
                        <button type="button" @click="closeModal" class="btn btn-info">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../axios';

const router = useRouter();

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    antiqueId: {
        type: [Number, String],
        required: false
    }
});

const emit = defineEmits(['close', 'success']);

const antique = ref({
    id: null,
    name: "",
    description: "",
    price: "",
    image: null,
});

const loading = ref(false);
const error = ref(null);
const dragActive = ref(false);
const imagePreview = ref(null);
const fileInput = ref(null);

// Watch for changes in show prop and antiqueId to fetch data
watch(() => [props.show, props.antiqueId], ([showValue, id]) => {
    if (showValue && id) {
        fetchAntique(id);
    }
}, { immediate: true });

function closeModal() {
    emit('close');
    resetForm();
}

function resetForm() {
    antique.value = {
        id: null,
        name: "",
        description: "",
        price: "",
        image: null,
    };
    imagePreview.value = null;
    error.value = null;
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            error.value = "L'image ne doit pas dépasser 5MB";
            return;
        }
        antique.value.image = file;
        handleFile(file);
        error.value = null;
    }
}

function handleDrop(e) {
    dragActive.value = false;
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.size > 5 * 1024 * 1024) {
            error.value = "L'image ne doit pas dépasser 5MB";
            return;
        }
        antique.value.image = file;
        handleFile(file);
        error.value = null;
    }
}

function handleFile(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
}

async function fetchAntique(id) {
    if (!id) return;
    
    try {
        loading.value = true;
        error.value = null;
        
        console.log('Fetching antique for edit:', id);
        
        const response = await api.get(`/api/antiques/${id}`);
        
        console.log('Antique data:', response.data);
        
        // Populate form with existing data
        antique.value = {
            id: response.data.id,
            name: response.data.name,
            description: response.data.description,
            price: response.data.price,
            image: null, // Don't set the file object
        };
        
        // Set image preview to existing image
        if (response.data.image_url || response.data.image) {
            imagePreview.value = response.data.image_url || response.data.image;
        }
        
    } catch (err) {
        console.error('Error fetching antique:', err);
        error.value = 'Erreur lors du chargement de l\'antiquité';
    } finally {
        loading.value = false;
    }
}

async function editAntique() {
    try {
        loading.value = true;
        error.value = null;

        const token = localStorage.getItem("token");
        if (!token && !window.Laravel?.isLoggedIn) {
            alert("Veuillez vous connecter pour modifier une antiquité.");
            closeModal();
            router.push("/login");
            return;
        }

        const formData = new FormData();
        formData.append("name", antique.value.name);
        formData.append("description", antique.value.description);
        formData.append("price", antique.value.price);
        formData.append("_method", "PUT"); // Laravel needs this for PUT requests with FormData
        
        // Only append image if a new one was selected
        if (antique.value.image instanceof File) {
            formData.append("image", antique.value.image);
        }

        await api.get("/sanctum/csrf-cookie");

        const response = await api.post(`/api/antiques/update/${antique.value.id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.data.success || response.status === 200) {
            alert("Antiquité modifiée avec succès!");
            emit('success');
            closeModal();
        } else {
            error.value = response.data.message || "Erreur lors de la modification";
        }
    } catch (err) {
        console.error("Erreur complète:", err);

        if (err.response?.status === 401) {
            alert("Session expirée. Veuillez vous reconnecter.");
            closeModal();
            router.push("/login");
        } else if (err.response?.status === 422) {
            const errors = err.response.data.errors || {};
            error.value = Object.values(errors).flat().join(" ");
        } else {
            error.value = err.response?.data?.message || "Erreur lors de la modification de l'antiquité";
        }
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; /* Increased z-index */
    padding: 1rem;
    overflow-y: auto;
}

.modal-content {
    background-color: #f9e0bb;
    border: 3px solid #2e1d19;
    border-radius: 12px;
    padding: 2rem;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    z-index: 10000;
}

.close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #2e1d19;
    color: #edc58a;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10001;
}

.close-btn:hover {
    background-color: #725042;
    transform: rotate(90deg);
}

.form-title {
    text-align: center;
    margin-bottom: 2rem;
    color: #edc58a;
    font-size: 2rem;
    font-weight: bold;
    padding-right: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2e1d19;
    font-weight: bold;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #2e1d19;
    border-radius: 8px;
    font-size: 1rem;
    background-color: #fff;
}

.form-control:focus {
    outline: none;
    border-color: #725042;
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    color: #725042;
    font-size: 0.9rem;
}

.alert-danger {
    background-color: #f8d7da;
    border: 2px solid #dc3545;
    color: #721c24;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: bold;
}

.dropzone {
    border: 2px dashed #2e1d19;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background-color: #fff;
}

.dropzone.drag-active {
    border-color: #725042;
    background-color: #edc58a;
}

.dropzone:hover {
    border-color: #725042;
}

.preview-image {
    max-width: 100%;
    max-height: 300px;
    height: auto;
    margin-top: 1rem;
    border-radius: 8px;
}

.form-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    flex: 1;
    padding: 1rem;
    border: 2px solid;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1rem;
}

.btn-primary {
    background-color: #2e1d19;
    border-color: #2e1d19;
    color: #f9e0bb;
}

.btn-primary:hover:not(:disabled) {
    background-color: #725042;
    transform: translateY(-2px);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-info {
    background-color: #725042;
    border-color: #725042;
    color: #f9e0bb;
}

.btn-info:hover {
    background-color: #2e1d19;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .modal-content {
        padding: 1.5rem;
    }
    
    .form-title {
        font-size: 1.5rem;
    }
    
    .form-buttons {
        flex-direction: column;
    }
}
</style>