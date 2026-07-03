@extends('wt.layouts.user')

@section('title', 'Profile Settings')
@section('page_title', 'Profile Settings')

@section('content')
@include('wt.profile._it_style', ['routePrefix' => 'wt.user'])
@endsection
