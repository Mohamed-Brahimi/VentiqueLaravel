@extends('layouts.app')


@section('content')

    <h1>Ajouter un article</h1>
    @if ($message = Session::get('warning'))

        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>

    @endif

    <form action="{{ route('antiques.store') }}" method="POST" enctype="multipart/form-data">
        @csrf


        <div class="form-group mb-3">
            <label for="name">Nom:</label>
            <input type="text" class="form-control" id="name" placeholder="Entrez un nom" name="name">
        </div>


        <div class="form-group mb-3">

            <label for="description">Description:</label>
            <textarea name="description" id="description" cols="30" rows="10" class="form-control"></textarea>
            <!-- <input type="hidden" name="user_id" value="<?= 1?>" /><br /> -->
        </div>

        <div class="form-group mb-3">

            <label for="price">Prix:</label>
            <input type="" name="price" id="price" class="form-control" placeholder="Entrez le prix" min="1"></input>
            <!-- <input type="hidden" name="user_id" value="<?= 1?>" /><br /> -->
        </div>
        <div class="form-group mb-3">

            <label for="image">Image:</label>
            <input name="image" id="image" placeholder="Entrez l'url de votre image" class="form-control"></input>
            <!-- <input type="hidden" name="user_id" value="<?= 1?>" /><br /> -->
        </div>
        <input type="hidden" name="user_id" value="<?= 1?>" /><br />
        <button type="submit" class="btn btn-primary">Publier</button> <a href="{{ url('/') }}" class="btn btn-info">Retour
            à la page d'accueil</a>

    </form>
@endsection