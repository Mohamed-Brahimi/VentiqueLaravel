@extends('layouts.app')

@section('content')
    <div class="form-container">
        <h1 class="form-title">@lang("antique_ajouter_article")</h1>

        @if ($message = Session::get('warning'))
            <div class="alert alert-warning">
                <p>{{ $message }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('antiques.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">@lang("antique_nom")</label>
                <input type="text" class="form-control" id="name" placeholder="Entrez un nom" name="name"
                    value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="description">@lang("antique_desc")</label>
                <textarea name="description" id="description" cols="30" rows="6" class="form-control"
                    placeholder="Décrivez votre antiquité..." required>{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="price">@lang("antique_prix")</label>
                <input type="number" name="price" id="price" class="form-control" placeholder="Entrez le prix" min="0.01"
                    step="0.01" value="{{ old('price') }}" required>
            </div>

            <div class="form-group">
                <label for="image">@lang("antique_image")</label>
                <input type="file" name="image" id="image" class="form-control"
                    accept="image/jpeg,image/png,image/jpg,image/gif" required>
            </div>

            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">@lang("antique_publier")</button>
                <a href="{{ url('/') }}" class="btn btn-info">@lang("antique_retour")</a>
            </div>
        </form>
    </div>
@endsection