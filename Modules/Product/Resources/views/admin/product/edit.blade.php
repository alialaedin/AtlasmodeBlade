@extends('admin.layouts.master')

@section('content')

<div class="page-header">
  <x-breadcrumb :items="[
		['title' => 'لیست محصولات', 'route_link' => 'admin.products.index'],
		['title' => 'ویرایش محصول']
	]"/>
</div>

<div id="app"></div>

@endsection

@section('scripts')

<script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>
<script src="{{ asset('assets/vue/multiselect/vue-multiselect.umd.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-jalaali@0.9.2/build/moment-jalaali.js"></script>
<script src="{{ asset('assets/vue/date-time-picker/vue3-persian-datetime-picker.umd.min.js') }}"></script>

<script>
  const { createApp } = Vue;
  createApp({
    components: {
      'multiselect': window['vue-multiselect'].default,
			'date-picker': Vue3PersianDatetimePicker,
    },
    mounted() {},
    data() {return {}},
    methods: {},
    watch: {},
    computed: {}
  }).mount('#app');
</script>

@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vue/multiselect/vue-multiselect.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/vue/multiselect/custom-styles.css') }}"/>
@endsection


