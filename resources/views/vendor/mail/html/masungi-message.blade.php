@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('masungi.url')])
            {{-- {{ config('masungi.name') }} --}}
            <img src="{{ asset('images/masungi_logo-v2.png')}}" alt="{{config('masungi.name') }}" style="width: 200px; position: relative;">
        @endcomponent
    @endslot

    {{-- Body --}}
    {!! html_entity_decode($message, ENT_QUOTES, 'UTF-8') !!}

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('masungi.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
