@extends('layouts.app')


@section('content')
    <div class="row">

        <div class="col-lg-10">
            <h2>@lang("antique_liste_antiques")</h2>
        </div>
        @if(Auth::check())

            <div class="col-lg-2">
                <a class="btn btn-success" href="{{ url('antiques/create') }}">@lang("antique_ajouter")</a>
            </div>
        @endif

    </div>



    @if ($message = Session::get('success'))

        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>

    @endif



    <div class="container-antiques">



        @foreach ($antiques->reverse() as $index => $antique)
            <div class="antique">
                <header class="antique-header">
                    <a href="{{ url('antiques/' . $antique->id) }}">
                        <h1 class="antique-nom">
                            {{ $antique->name }}
                        </h1>
                    </a>

                    @if ($antique->image)
                        <div class="image">
                            <img src="{{ asset('storage/' . $antique->image) }}" alt="{{ $antique->name }}">
                        </div>
                    @endif

                    <h3 class="antique-desc">{{ $antique->description }}</h3>
                </header>
                <p class="antique-prix">{{ $antique->price }}$</p>
            </div>
        @endforeach

    </div>
@endsection