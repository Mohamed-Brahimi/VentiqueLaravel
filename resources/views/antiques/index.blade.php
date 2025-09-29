@extends('layouts.app')


@section('content')
    <div class="row">

        <div class="col-lg-10">
            <h2>Liste des antiques</h2>
        </div>

        <div class="col-lg-2">
            <a class="btn btn-success" href="{{ url('antiques/create') }}">Ajouter une antique</a>
        </div>

    </div>



    @if ($message = Session::get('success'))

        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>

    @endif



    <div class="container">

        <div class="row">

            @foreach ($antiques->reverse() as $index => $antique)
                <div class="col-md-4">

                    <a href="{{ url('antiques/' . $antique->id) }}">
                        <h2>
                            {{ $antique->name }}
                        </h2>

                    </a>

                    {{ $antique->description }}

                </div>
            @endforeach
        </div>
    </div>
@endsection