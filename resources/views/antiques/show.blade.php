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
                    <form action="{{ url('antiques/' . $antique->id) }}" method="POST" style="display: inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
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
                    <form action="{{ url('offre/' . $offer->id) }}" method="POST" style="display: inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            {{-- <div class="container-offres-enchere">
                <span>#{{$offer->id}}</span>
                <span>{{$offer->created_at}}</span>
                <span>{{$offer->price}}$</span>
                <form action="{{ url('offre/' . $offer->id) }}" method="POST" style="display: inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div> --}}
        @endforeach
        </table>

        <h4>Ajouter une offre:</h4>
        <div class="form-group mb-4">

                    @if ($message = Session::get('warning'))

                        <div class="alert alert-warning">
                            <p>{{ $message }}</p>
                        </div>

                    @endif

                    {{-- <form action="{{ url(url('antiques/'. $antique->id). '/offers') }}" method="POST"
                        enctype="multipart/form-data"> --}}
                        <form action="{{route('offers.store')}}" method="POST" enctype="multipart/form-data">

                        <div class="form-group mb-3">

                            <label for="content">Ajouter votre offres:</label>
                            <input type="number" name="price" id="price" />
                            <input type="hidden" name="user_id" value=1 /><br />

                            <input type="hidden" name="antique_id" value="{{ $antique->id}}" /><br />
                        </div>

                        <button type="submit" class="btn btn-primary">Publier</button>
                    </form>
        </div>
    </div>

@endsection
