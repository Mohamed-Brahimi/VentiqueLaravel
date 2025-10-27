@extends('layouts.app')


@section('content')



    <div class="container">
        <div class="row">
            <p> <B>@lang("apropos_texte")</B></p>
            <h2> @lang("apropos_texte_image")</h2>

            <h2>@lang("apropos_noms")</h2>
            <ul>
                <li>@lang("apropos_cours1")</li>
                <li>@lang("apropos_cours2")</li>
            </ul>

            <ul>
                <li>@lang("apropos_interactions1")</li>
                <li>@lang("apropos_interactions2")</li>
                <li>@lang("apropos_interactions3")</li>
                
            </ul>
            

        </div>
@endsection