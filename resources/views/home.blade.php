@extends('layouts.site')

@section('title', 'Home')

@section('content')
  @include('partials.home.hero')
  @include('partials.home.courses')
  @include('partials.home.franchises')
  @include('partials.home.free-demo')
  @include('partials.home.why')
  @include('partials.home.faq')
  @include('partials.home.contact')
@endsection
