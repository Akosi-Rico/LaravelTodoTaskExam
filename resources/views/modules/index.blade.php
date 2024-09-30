@extends('layouts.app')
@section("body")
    <parent
        :baseimage="{{ json_encode(asset("image")) }}"
        :data="{{ json_encode($data) }}"
        :storagepath="{{ json_encode(asset("storage/temporary")) }}">
    </parent>
@endsection


