@extends('layouts.app')

@section('content')
    <div class="form-container">
        <h1 class="form-title">@lang("antique_modifier") {{ $antique->name }}</h1>

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

        <form method="post" action="{{ url('antiques/' . $antique->id) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf

            <div class="form-group">
                <label for="name">@lang("antique_nom")</label>
                <input type="text" class="form-control" id="name" placeholder="Entrer nom" name="name"
                    value="{{ old('name', $antique->name) }}" required>
            </div>

            <div class="form-group">
                <label for="description">@lang("antique_ajouter_desc")</label>
                <textarea name="description" id="description" cols="30" rows="6" class="form-control"
                    required>{{ old('description', $antique->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="price">@lang("antique_entrer_prix_min")</label>
                <input type="number" class="form-control" id="price" min="0.01" step="0.01" name="price"
                    value="{{ old('price', $antique->price) }}" required>
            </div>

            <div class="form-group">
                <label for="image">@lang("antique_image")</label>
                @if($antique->image)
                    <div style="margin-bottom: 0.5rem;">
                        <small style="color: #725042;">Image actuelle:</small><br>
                        <img src="{{ asset('storage/' . $antique->image) }}" alt="Image actuelle"
                            style="max-width: 200px; border-radius: 5px; border: 2px solid #edc58a;">
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control"
                    accept="image/jpeg,image/png,image/jpg,image/gif">
                <small style="color: #725042;">Laissez vide pour conserver l'image actuelle</small>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">@lang("antique_enregistrer")</button>
                <a href="{{ url('antiques/' . $antique->id) }}" class="btn btn-info">@lang("antique_annuler")</a>
            </div>
        </form>
    </div>
@endsection