@extends('layouts.app')


@section('content')


    <h1>@lang("antique_modifier") {{ $antique->name }}</h1>


    <h1>@lang("antique_ajouter")</h1>
    @if ($message = Session::get('warning'))

        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>

    @endif

    <form method="post" action="{{ url('antiques/' . $antique->id) }}">
        @method('PATCH')
        @csrf


        <div class="form-group mb-3">

            <label for="name">@lang("antique_nom")</label>
            <input type="text" class="form-control" id="name" placeholder="Entrer nom" name="name"
                value="{{ $antique->name }}">

        </div>

        <div class="form-group mb-3">

            <label for="description">@lang("antique_ajouter_desc")</label>
            <textarea name="description" id="description" cols="30" rows="10"
                class="form-control">{{ $antique->description }}</textarea>

        </div>

        <div class="form-group mb-3">

            <label for="price">@lang("antique_entrer_prix_min")</label>
            <input type="number" class="form-control" id="price" min=1 name="price" value="{{ $antique->price }}">

        </div>

        <div class="form-group mb-3">

            <label for="image">@lang("antique_image")</label>
            <input type="text" class="form-control" id="image" placeholder="Entrer URL image" name="image"
                value="{{ $antique->image }}">

        </div>
        <button type="submit" class="btn btn-primary">@lang("antique_enregistrer")</button>

        <a href="{{ url('antiques/' . $antique->id) }}" class="btn btn-info">@lang("antique_annuler")</a>
    </form>


@endsection