@extends('admin.layouts.master')

@section('styles')
	<style>
		.card {  
			transition: background-color 0.5s ease, color 0.3s ease; 
		}  
	</style>
@endsection

@section('content')

	<div class="page-header">
		<x-breadcrumb :items="[
			['title' => 'لیست حمل و نقل ها', 'route_link' => 'admin.shippings.index'],
			['title' => 'نمایش حمل و نقل', 'route_link' => 'admin.shippings.show', 'parameter' => $shipping],
			['title' => 'شهر ها'],
		]" />
	</div>

	<form 
		id="assign-cities-form"
		action="{{ route('admin.shippings.assign-cities', $shipping) }}"
		method="POST"
		class="d-none">
		@csrf
	</form>

	<div id="shipping-provinces-box-row" class="row">
		@foreach ($shipping->provinces ?? [] as $province)
			<div class="col-xl-2 col-lg-4 col-md-6 shipping-province-box" data-shipping-province-row-id="shipping-province-row-{{ $loop->iteration }}" onclick="showCitiesRow(event)">
				<div class="card shipping-province-card" style="cursor: pointer;">
					<div class="card-body px-0">
						<div class="row">
							<div class="col-12 d-flex justify-content-center align-items-center">
								<b class="fs-16 discount-amount">{{ $province->name }}</b>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>

	@foreach ($shipping->provinces ?? [] as $province)
		<div id="shipping-province-row-{{ $loop->iteration }}" class="row shipping-province-row">
			<div class="col-12">
				<x-card>
					<x-slot name="cardTitle">لیست شهر ها</x-slot>
					<x-slot name="cardBody">

						<div class="row">
							<div class="col-3 form-group">
								<select class="form-control cities-select-box">
									<option value="">انتخاب</option>
									@foreach ($province->cities ?? [] as $city)
										<option value="{{ $city->id }}" data-city-name="{{ $city->name }}">{{ $city->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<x-table-component>
							<x-slot name="tableTh">
								<tr>
									<th>شهر</th>
									<th>هزینه ارسال (تومان)</th>
									<th>عملیات</th>
								</tr>
							</x-slot>
							<x-slot name="tableTd">
								@foreach ($shipping->cities->where('province_id', $province->id) ?? [] as $city)
									<tr>
										<td class="d-none">
											<input class="city-id-hidden-input" value="{{ $city->id }}" hidden>
										</td>
										<td class="city-name">{{ $city->name }}</td>
										<td>
											<input type="text" class="form-control price-input comma" value="{{ number_format($city->pivot->price) }}">
										</td>
										<td>
											<button class="btn btn-danger btn-sm btn-icon" onclick="removeCityRow(event)">حذف</button>
										</td>
									</tr>
								@endforeach
							</x-slot>
						</x-table-component>

						<div class="row">
							<div class="col-12">
								<div>
									<button class="btn btn-info btn-sm" onclick="syncCities()">بروزرسانی شهر ها</button>
								</div>
							</div>
						</div>

					</x-slot>
				</x-card>
			</div>
		</div>
	@endforeach

	<div id="Examples">
		<table>
			<tbody>
				<tr id="example-tr">
					<td class="d-none"><input class="city-id-hidden-input" hidden></td>
					<td class="city-name"></td>
					<td><input type="text" class="form-control price-input comma" value=""></td>
					<td><button class="btn btn-danger btn-sm btn-icon" onclick="removeCityRow(event)">حذف</button></td>
				</tr>
			</tbody>
		</table>
	</div>

@endsection

@section('scripts')
	<script>

		const exampleTr = $('#example-tr').clone().removeAttr('id');

		let hideShippingProvinceRows = () => $('.shipping-province-row').hide();
		let removeCityRow = (event) => $(event.target).closest('tr').remove();  
		let removeExamplesFromDOM = () => $('#Examples').remove();

		class CityInput {  

			constructor(inputName, inputValue) {  
				this.name = inputName;  
				this.value = inputValue;  
				this.makeInput();  
			}  

			makeInput() {
				let input = $('<input name="" value="" />');
				input.attr('name', this.name);
				input.attr('value', this.value);
				this.input = input;
				$('#assign-cities-form').append(this.input);
			}

		}  

		function showCitiesRow(event) {   

			hideShippingProvinceRows();  

			const selectedCardClassName = 'bg-gradient-teal';  
			$('.shipping-province-box').each(function() {  
				let card = $(this).find('.card');   
				if (card.hasClass(selectedCardClassName)) {  
					card.removeClass([selectedCardClassName, 'text-white']);  
				}  
			});  

			let currentCard = $(event.currentTarget).find('.card');  
			currentCard.addClass([selectedCardClassName, 'text-white']);  

			let shippingProvinceRowID = $(event.currentTarget).data('shipping-province-row-id');  
			let shippingProvinceRow = $('.shipping-province-row').filter(function() {  
				return $(this).attr('id') === shippingProvinceRowID;  
			});  

			if (shippingProvinceRow.length) {  
				shippingProvinceRow.fadeToggle(500);   
			}  
		}  

		function handleChangeEventOnCitiesSelectBox() {  
			$('.shipping-province-row').each(function () {  
				
				const shippingProvinceRow = $(this);  
				const citiesSelectBox = shippingProvinceRow.find('.cities-select-box');  

				citiesSelectBox.on('select2:select', (event) => {  

						const cityId = event.target.value;  
						const selectedOption = citiesSelectBox.find('option:selected');  
						const cityName = selectedOption.data('city-name');   
						const table = shippingProvinceRow.find('table');  

						const isCityExists = table.find('tbody tr').filter(() => {  
							return $(this).find('.city-id-hidden-input').val() == cityId;
						});  

						if (isCityExists.length) return;

						const tr = exampleTr.clone();  

						tr.find('.city-id-hidden-input').val(cityId);  
						tr.find('.city-name').text(cityName);  
						table.find('tbody').append(tr);  
						comma();
				});  
			});  
		}  

		function makeCitiesSelectBoxLabels() {
			$('.shipping-province-row').each(function() {
				$(this).find('.cities-select-box').select2({
					placeholder: 'انتخاب شهر ها'
				});
			});
		}

		function syncCities() {

			$('.shipping-province-row').each(function () {

				let index = 0;
				let row = $(this);

				row.find('table tbody tr').each(function () {

					let id = $(this).find('.city-id-hidden-input').val();
					let price = $(this).find('.price-input').val()?.replace(/,/g, "");

					if (price < 1) return;

					new CityInput(`cities[${index}][id]`, id);
					new CityInput(`cities[${index}][price]`, price);

					index++
					$('#assign-cities-form').submit();

				});

			});

		} 

		makeCitiesSelectBoxLabels();

		$(document).ready(() => {
			removeExamplesFromDOM();
			hideShippingProvinceRows();
			handleChangeEventOnCitiesSelectBox();
		});

	</script>
@endsection
