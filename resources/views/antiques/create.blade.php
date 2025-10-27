@extends('layouts.app')


@section('content')

    <h1>@lang("antique_ajouter_article")</h1>
    @if ($message = Session::get('warning'))

        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>

    @endif

    <form action="{{ route('antiques.store') }}" method="POST" enctype="multipart/form-data">
        @csrf


        <div class="form-group mb-3">
            <label for="name">@lang("antique_nom")</label>
            <input type="text" class="form-control" id="name" placeholder="Entrez un nom" name="name">
        </div>


        <div class="form-group mb-3">

            <label for="description">@lang("antique_desc")</label>
            <textarea name="description" id="description" cols="30" rows="10" class="form-control"></textarea>
            <!-- <input type="hidden" name="user_id" value="<?= 1?>" /><br /> -->
        </div>

        <div class="form-group mb-3">

            <label for="price">@lang("antique_prix")</label>
            <input type="" name="price" id="price" class="form-control" placeholder="Entrez le prix" min="1"></input>
            <!-- <input type="hidden" name="user_id" value="<?= 1?>" /><br /> -->
        </div>
        <div class="form-group mb-3">

            <label for="image">@lang("antique_image")</label>
            <input type="file" name="image" id="image" accept="image/*">
            <!-- <input type="hidden" name="user_id" value="<?= 1?>" /><br /> -->
        </div>
        <input type="hidden" name="user_id" value="<?= auth()->id() ?>" /><br />
        <button type="submit" class="btn btn-primary">@lang("antique_publier")</button> <a href="{{ url('/') }}" class="btn btn-info">@lang("antique_retour")</a>

    </form>
@endsection