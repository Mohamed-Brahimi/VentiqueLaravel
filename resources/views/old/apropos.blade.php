@extends('layouts.app')

@section('content')
    <div class="form-container">
        <h1 class="form-title">@lang("footer_apropos")</h1>

        <div style="text-align: left; line-height: 1.8;">
            <div style="margin-bottom: 2rem;">
                <p style="font-size: 1.2rem; font-weight: bold; color: #2e1d19;">
                    @lang("apropos_texte")
                </p>
            </div>

            <div style="margin-bottom: 2rem;">
                <h2 style="color: #2e1d19; border-bottom: 2px solid #edc58a; padding-bottom: 0.5rem;">
                    @lang("apropos_noms")
                </h2>
                <div style="margin-left: 1rem; margin-top: 1rem;">
                    <p style="margin-bottom: 0.5rem;">• @lang("apropos_cours1")</p>
                    <p style="margin-bottom: 0.5rem;">• @lang("apropos_cours2")</p>
                </div>
            </div>
            <div style="margin-bottom: 2rem;">
                <h2 style="color: #2e1d19; border-bottom: 2px solid #edc58a; padding-bottom: 0.5rem;">
                    Schema
                </h2>
                <img src="{{ asset('storage/schema.png') }}" alt="À propos"
                    style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid #edc58a;">
            </div>
            <div style="margin-bottom: 2rem;">
                <h3 style="color: #2e1d19; border-bottom: 2px solid #edc58a; padding-bottom: 0.5rem;">
                    Fonctionnalités principales:
                </h3>
                <div style="margin-left: 1rem; margin-top: 1rem;">
                    <p style="margin-bottom: 0.5rem;">• @lang("apropos_interactions1")</p>
                    <p style="margin-bottom: 0.5rem;">• @lang("apropos_interactions2")</p>
                    <p style="margin-bottom: 0.5rem;">• @lang("apropos_interactions3")</p>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="color: #2e1d19; border-bottom: 2px solid #edc58a; padding-bottom: 0.5rem;">
                    Technologies utilisées:
                </h3>
                <div style="margin-left: 1rem; margin-top: 1rem;">
                    <p style="margin-bottom: 0.5rem;">• Laravel 11 (Framework PHP)</p>
                    <p style="margin-bottom: 0.5rem;">• MySQL (Base de données)</p>
                    <p style="margin-bottom: 0.5rem;">• Bootstrap & CSS personnalisé (Interface utilisateur)</p>
                    <p style="margin-bottom: 0.5rem;">• JavaScript & jQuery (Interactions dynamiques)</p>
                </div>
            </div>

            <div class="form-buttons" style="margin-top: 3rem;">
                <a href="{{ url('/') }}" class="btn btn-primary">
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
@endsection