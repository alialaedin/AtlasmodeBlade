@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
		<x-breadcrumb :items="[['title' => 'گروه های پیشنهادی']]" />
  </div>

  <div class="row">
    @foreach ($allGroups as $group)
      <div class="col-xl-4 col-lg-6 col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="mt-0 text-center">
                  <a href="{{ route('admin.recommendations.index', ['group' => $group['name']]) }}">
                    <span class="fs-20 font-weight-semibold"> {{ $group['label'] }} </span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

@endsection
