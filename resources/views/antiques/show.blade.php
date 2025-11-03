@extends('layouts.app')


@section('content')
    <div class="container">

        <div class="row">
            <div class="antique-enchere">
                <header>
                    <h1 class="antique-enchere-nom">{{ $antique->name }} </h1>

                    @if ($antique->image)
                        <div class="image">
                            <img src="{{ asset('storage/' . $antique->image) }}" alt="{{ $antique->name }}">
                        </div>
                    @endif
                    <h3 class="antique-enchere-desc">{{ $antique->description }} </h3>
                    <strong>@lang("antique_cree_le") {{ $antique->created_at }} </strong>

                </header>
                <p class="antique-enchere-prix">@lang("antique_prix_min") {{ $antique->price }} $</p>

                <div class="antique-enchere-options">
                    @if(Auth::check() && (Auth::user()->id == $antique->user_id))

                        <a href="{{ url('antiques/' . $antique->id . '/edit') }}" class="btn btn-info"
                            style="margin-right: .5rem;">@lang("modifier")</a>

                        <form action="{{ url('antiques/' . $antique->id) }}" method="POST"
                            style="display: inline; margin-right: .5rem;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette antiquité?')">@lang("supprimer")</button>
                        </form>

                        <a href="{{ url('/') }}" class="btn btn-info">@lang("antique_retour")</a>

                    @endif
                </div>
            </div>
        </div>

        <div class="offres">
            <h2>@lang("offre_les_offres")</h2>
        </div>

        <table class="offer-table">
            <tr>
                <th>@lang("offre_table_numero")</th>
                <th>@lang("offre_table_creele")</th>
                <th>@lang("offre_table_prix")</th>
                <th>@lang("offre_table_action")</th>
            </tr>
            @foreach ($antique->offers as $index => $offer)
                <tr>
                    <td>#{{ $index + 1 }}</td>
                    <td>{{ $offer->created_at }}</td>
                    <td>{{ $offer->price }}$</td>
                    <td>
                        @if(Auth::check() && (Auth::user()->id == $offer->user_id || Auth::user()->role === 'ADMIN'))
                            <form action="{{ route('offers.destroy', $offer->id) }}" method="POST" style="display: inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre?')">@lang("supprimer")</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        <h4>@lang("offre_ajouter1")</h4>
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

            @if(Auth::check())
                <form action="{{ route('offers.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="price">@lang("offre_ajouter2")</label>
                        <input type="number" name="price" id="price" step="0.01" min="0" required />
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" />
                        <input type="hidden" name="antique_id" value="{{ $antique->id }}" />
                    </div>

                    <button type="submit" class="btn btn-primary">Publier</button>
                </form>
            @endif

        </div>
    </div>

@endsection