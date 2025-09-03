@extends('layouts.psi.operasional.main')
@section('content')

<div class="row mt-1">
    <div class="card">
        <div class="card-header">
            <div class="card-title text-uppercase fw-bold text-center my-1">
                input laporan pengawasan rutin
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('store.rutin') }}" method="POST" enctype="multipart/form-data">
                @include('psi.operasional.rutin._form')
            </form>
        </div>
    </div>
</div>

@endsection
