<template>
    <div class="form-container">
        <h1 class="form-title">{{ translations.footer_apropos }}</h1>

        <div class="apropos-content">
            <section class="intro">
                <p class="lead">{{ translations.apropos_texte }}</p>
            </section>

            <section class="names">
                <h2 class="section-title">{{ translations.apropos_noms }}</h2>
                <div class="list">
                    <p>• {{ translations.apropos_cours1 }}</p>
                    <p>• {{ translations.apropos_cours2 }}</p>
                </div>
            </section>

            <section class="schema">
                <h2 class="section-title">Schema</h2>
                <img :src="imageSrc" alt="À propos" class="schema-img" />
            </section>

            <section class="features">
                <h3 class="section-subtitle">Fonctionnalités principales:</h3>
                <div class="list">
                    <p>• {{ translations.apropos_interactions1 }}</p>
                    <p>• {{ translations.apropos_interactions2 }}</p>
                    <p>• {{ translations.apropos_interactions3 }}</p>
                </div>
            </section>

            <section class="techs">
                <h3 class="section-subtitle">Technologies utilisées:</h3>
                <div class="list">
                    <p>• Laravel 11 (Framework PHP)</p>
                    <p>• MySQL (Base de données)</p>
                    <p>
                        • Bootstrap & CSS personnalisé (Interface utilisateur)
                    </p>
                    <p>• JavaScrip & jQuery (Interactions dynamiques)</p>
                </div>
            </section>

            <div class="form-buttons">
                <router-link to="/" class="btn btn-primary"
                    >Retour à l'accueil</router-link
                >
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Apropos",
    props: {
        translations: {
            type: Object,
            default() {
                return {
                    footer_apropos: "À propos",
                    apropos_texte:
                        "Cette page contiendra des détails sur notre site.",
                    apropos_noms:
                        "Développeurs: Mohamed Arezki Brahimi, Adam Mahmoudi",
                    apropos_cours1:
                        "420-5H6 MO Applications Web Transactionnelles.",
                    apropos_cours2: "Automne 2025, Collège Montmorency.",
                    apropos_interactions1:
                        "Se connecter ou s'inscrire sur le site",
                    apropos_interactions2:
                        "Ajouter, modifier ou supprimer une antiquité",
                    apropos_interactions3:
                        "Faire des offres sur les antiquités",
                };
            },
        },
        imageSrc: {
            type: String,
            default: () => "/storage/schema.png",
        },
    },
};
</script>

<style scoped>
.form-container {
    padding: 1.5rem;
}
.form-title {
    font-size: 1.6rem;
    margin-bottom: 1rem;
}
.apropos-content {
    text-align: left;
    line-height: 1.8;
}
.intro .lead {
    font-size: 1.2rem;
    font-weight: bold;
    color: #2e1d19;
    margin-bottom: 2rem;
}
.section-title {
    color: #2e1d19;
    border-bottom: 2px solid #edc58a;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}
.section-subtitle {
    color: #2e1d19;
    border-bottom: 2px solid #edc58a;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}
.list {
    margin-left: 1rem;
    margin-top: 1rem;
}
.schema-img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    border: 2px solid #edc58a;
}
.form-buttons {
    margin-top: 3rem;
}
</style>
