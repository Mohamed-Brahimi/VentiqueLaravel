@extends('layouts.app')


@section('content')


    <h1>Modifier l'antique: {{ $antique->name }}</h1>


    <h1>Ajouter un antique</h1>
    @if ($message = Session::get('warning'))

        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>

    @endif

    <form method="post" action="{{ url('antiques/' . $antique->id) }}">
        @method('PATCH')
        @csrf


        <div class="form-group mb-3">

            <label for="name">Nom:</label>
            <input type="text" class="form-control" id="name" placeholder="Entrer nom" name="name"
                value="{{ $antique->name }}">

        </div>

        <div class="form-group mb-3">

            <label for="description">Ajouter la description:</label>
            <textarea name="description" id="description" cols="30" rows="10"
                class="form-control">{{ $antique->description }}</textarea>

        </div>

        <div class="form-group mb-3">

            <label for="price">Entrer votre prix minimum </label>
            <input type="number" class="form-control" id="price" min=1 name="price" value="{{ $antique->price }}">

        </div>

        <div class="form-group mb-3">

            <label for="image">Image:</label>
            <input type="text" class="form-control" id="image" placeholder="Entrer URL image" name="image"
                value="{{ $antique->image }}">

        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>

        <a href="{{ url('antiques/' . $antique->id) }}" class="btn btn-info">Annuler</a>
    </form>


@endsection