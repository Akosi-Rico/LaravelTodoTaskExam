@extends('layouts.app')
@section("body")
    <login
        :baseimage="{{ json_encode(asset("image")) }}"
        :loginurl="{{ json_encode(route("login.process")) }}"
        :baseurl="{{ json_encode(route("login.index") )}}"
        :registerurl=" {{ json_encode(route("login.create"))}}">
    </login>
@endsection
