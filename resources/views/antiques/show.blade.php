@extends('layouts.app')


@section('content')
    <div class="container">

        <div class="row">
            <div class="antique-enchere">
                <header>
                    @if ($antique->image)
                        <div class="image">
                            <img src="{{ asset('storage/' . $antique->image) }}" alt="{{ $antique->name }}">
                        </div>
                    @endif
                    <h1 class="antique-enchere-nom">{{ $antique->name }} </h1>
                    <h3 class="antique-enchere-desc">{{ $antique->description }} </h3>
                    <strong>Crée le: {{ $antique->created_at }} </strong>

                </header>
                <p class="antique-enchere-prix">Avec comme minimum : {{ $antique->price }} $</p>

                <div class="antique-enchere-options">
                    <a href="{{ url('antiques/' . $antique->id . '/edit') }}" class="btn btn-info">Modifier</a>
                    <a href="{{ url('/') }}" class="btn btn-info">Retour à la page d'accueil</a>
                    @if(Auth::check() && (Auth::user()->id == $antique->user_id || Auth::user()->is_admin))
                        <form action="{{ url('antiques/' . $antique->id) }}" method="POST" style="display: inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="offres">
            <h2>Les offres:</h2>
        </div>
        <table class="offer-table">
            <tr>
                <th>Numero</th>
                <th>Crée le</th>
                <th>Prix</th>
                <th>Action</th>
            </tr>
            @foreach ($antique->offers as $offer)
                <tr>
                    <td>#{{$offer->id}}</td>
                    <td>{{$offer->created_at}}</td>
                    <td>{{$offer->price}}$</td>
                    <td>
                        @if(Auth::check() && (Auth::user()->id == $offer->user_id || Auth::user()->is_admin))
                            <form action="{{ route('offers.destroy', $offer->id) }}" method="POST" style="display: inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        @endif
                    </td>
                </tr>

            @endforeach
        </table>

        <h4>Ajouter une offre:</h4>
        <div class="form-group mb-4">

            @if ($message = Session::get('warning'))

                <div class="alert alert-warning">
                    <p>{{ $message }}</p>
                </div>

            @endif

            @if ($message = Session::get('success'))

                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>

            @endif

            <form action="{{route('offers.store')}}" method="POST">
                @csrf

                <div class="form-group mb-3">

                    <label for="price">Ajouter votre offre:</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" required />
                    @if(Auth::check())
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" />
                    @endif
                    <input type="hidden" name="antique_id" value="{{ $antique->id}}" />
                </div>

                <button type="submit" class="btn btn-primary">Publier</button>
            </form>
        </div>
    </div>

@endsection