@extends("vaahcms::backend.vaahone.layouts.backend")

@section('vaahcms_extend_backend_css')

@endsection


@section('vaahcms_extend_backend_js')

    @if(env('APP_MODULE_FORMS_ENV') == 'develop')
        <script src="http://localhost:8080/forms/assets/build/app.js" defer></script>
    @else
        <script src="{{vh_module_assets_url("Forms", "build/app.js")}}"></script>
    @endif

@endsection

@section('content')

    <div id="appForms">

        <router-view></router-view>

        <vue-progress-bar></vue-progress-bar>

    </div>

@endsection
