@extends('layouts.agency', ['title' => 'Edit Pekerja'])

@section('content')
<livewire:agency.worker-form worker="{{ $worker }}" />
@endsection
