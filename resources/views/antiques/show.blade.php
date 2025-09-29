@extends('layouts.app')


@section('content')
    <div class="container">


        <div class="row">
            <div class="col-md-12">
                <h1>{{ $antique->name }} </h1><strong>Crée le: {{ $antique->created_at }} </strong>
                <p class="lead">{{ $antique->description }}</p>

                <div class="buttons">
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

        <div class="container">
            <h2> Les offres:</h2>
            @foreach ($antique->offers as $offre)
                <strong> offre numéro {{$offre->id}} rédigé par:moi le {{ $offre->created_at }}
                </strong>
                <h3>{{ $offre->created_at }}</h2>
                    <p class="lead">{{ $offre->price }}</p>
                    <div class="buttons">
                        <form action="{{ url('offre/' . $offre->id) }}" method="POST" style="display: inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
            @endforeach

                <h4> Ajouter une offre:</h4>
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
                                {{-- <input type="hidden" value="{{ $antique->id}}">{{ $antique->id }}/><br /> --}}
                            </div>

                            <button type="submit" class="btn btn-primary">Publier</button>
                        </form>
                </div>
@endsection