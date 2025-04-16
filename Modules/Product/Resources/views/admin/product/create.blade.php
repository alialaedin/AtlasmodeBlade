@extends('admin.layouts.master')

@section('content')

<div class="page-header">
  <x-breadcrumb :items="[
		['title' => 'لیست محصولات', 'route_link' => 'admin.products.index'],
		['title' => 'ثبت محصول']
	]"/>
</div>

<div id="app" class="mb-5">
	<div class="row">
		<div class="col-xl-8">

			{{-- product details --}}
			<x-card>
				<x-slot name="cardTitle">اطلاعات محصول</x-slot>
				<x-slot name="cardBody">

					{{-- title --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="product-title">نام محصول :<span class="text-danger">&starf;</span></label>
						</div>
						<div class="col-xl-10">
							<input type="text" placeholder="نام محصول" class="form-control" v-model="product.title" id="product-title" required/>
						</div>
					</div>

					{{-- quantity --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="product-quantity">موجودی :</label>
						</div>
						<div class="col-xl-10">
							<input type="number" placeholder="موجودی" class="form-control" v-model="product.quantity" id="product-quantity"/>
						</div>
					</div>

					{{-- barcode --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="product-barcode">بارکد :</label>
						</div>
						<div class="col-xl-10">
							<input type="text" placeholder="بارکد" class="form-control" v-model="product.barcode" id="product-barcode" readonly/>
						</div>
					</div>

					{{-- SKU --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="product-SKU">SKU :</label>
						</div>
						<div class="col-xl-10">
							<input type="text" placeholder="SKU" class="form-control" v-model="product.SKU" id="product-SKU"/>
						</div>
					</div>

					{{-- categories --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="categories-select">
								دسته بندی ها
								<span class="text-danger">&starf;</span>
							</label>
						</div>
						<div class="col-xl-10">
							<multiselect
								dir="rtl"
								id="categories-select"
								class="custom-multiselect"
								v-model="product.categories"
								label="title"
								multiple
								placeholder="انتخاب دسته بندی ها"
								select-label="برای انتخاب دسته بندی کلیک کنید"
								deselect-label="برای حذف دسته بندی کلیک کنید"
								selected-label="انتخاب شده"
								track-by="id"
								:options="categories"
								:close-on-select="false"
								:searchable="true"
								required
							></multiselect>
						</div>
					</div>

					{{-- brand --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="brand-select">برند</label>
						</div>
						<div class="col-xl-10">
							<multiselect 
								dir="rtl" 
								id="brand-select" 
								v-model="product.brand"
								class="custom-multiselect"
								label="name"
								placeholder="انتخاب برند" 
								:options="brands">
							</multiselect>		
						</div>
					</div>

					{{-- unint --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="unit-select">واحد :<span class="text-danger">&starf;</span></label>
						</div>
						<div class="col-xl-10">
							<multiselect 
								dir="rtl" 
								id="unit-select" 
								class="custom-multiselect"
								v-model="product.unit"
								placeholder="انتخاب واحد" 
								label="name"
								required
								:options="units">
							</multiselect>		
						</div>
					</div>

					{{-- tags --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="tag-select">تگ ها</label>					
						</div>
						<div class="col-xl-10">
							<multiselect 
								dir="rtl" 
								id="tags-select" 
								class="custom-multiselect"
								v-model="product.tags"
								placeholder="انتخاب تگ ها" 
								label="name"
								:multiple="true"
								track-by="id"
								:close-on-select="false"
								:options="tags">
							</multiselect>	
						</div>
					</div>

					{{-- image_alt --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-2">
							<label for="product-image_alt">alt محصول :<span class="text-danger">&starf;</span></label>
						</div>
						<div class="col-xl-10">
							<input type="text" placeholder="alt محصول" class="form-control" v-model="product.image_alt" id="product-image_alt"/>
						</div>
					</div>

				</x-slot>
			</x-card>

			{{-- description --}}
			<x-card>
				<x-slot name="cardTitle">توضیحات محصول</x-slot>
				<x-slot name="cardBody">

					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="product-short-description">توضیحات کوتاه</label>
								<textarea id="product-short-description" rows="2" class="form-control" v-model="product.short_description"></textarea>
							</div>
						</div>
						{{-- <div class="col-12">
							<div class="form-group">
								<label for="product-meta-description">توضیحات</label>
								<textarea class="ckeditor form-control" id="product-description" v-model="product.description"></textarea>
							</div>
						</div> --}}
					</div>

				</x-slot>
			</x-card>

			{{-- varieties --}}
			<x-card>
				<x-slot name="cardTitle">تنوع‌ها</x-slot>
				<x-slot name="cardOptions">
					<div class="card-options">
						<button 
							type="button" 
							data-toggle="modal"
							data-target="#set-price-for-varieties"
							:disabled="isVarietiesEmpty"
							class="btn btn-sm btn-outline-info">
							قیمت گذاری کلی
						</button>
					</div>
				</x-slot>
				<x-slot name="cardBody">

					{{-- attributes select-box --}}
					<div class="row align-items-center mb-3">
						<div class="col-xl-2">
							<label for="attributes-select">ویژگی‌ها <span class="text-danger">&starf;</span></label>
						</div>
						<div class="col-xl-10">
							<multiselect
								dir="rtl"
								label="label"
								track-by="id"
								:options="uniqueAttributes"
								v-model="product.attributes"
								class="custom-multiselect"
								id="attributes-select"
								placeholder="انتخاب ویژگی ها"
								:multiple="true"
								:close-on-select="false"
								required
							></multiselect>
						</div>
					</div>

					{{-- attribute values select-box --}}
					<div class="row align-items-center my-2" v-for="(attribute, productAttributeIndex) in product.attributes" :key="productAttributeIndex">
						<div class="col-xl-2">
							<label :for="'attribute-select-' + productAttributeIndex">
								ویژگی 
								<b v-text="attribute.label"></b>
								<span class="text-danger">&starf;</span>
							</label>
						</div>
						<div class="col-xl-10">
							<multiselect
								:id="'attribute-select-' + productAttributeIndex"
								dir="rtl"
								label="value"
								track-by="id"
								class="custom-multiselect"
								:taggable="attribute.type === 'text'"
								v-model="product.attribute_values[attribute.id]"
								@tag="(value) => addNewTag(value, attribute)"
								@remove="clearVarietyValues()"
								@select="clearVarietyValues()"
								placeholder="انتخاب مقادیر ویژگی"
								:multiple="true"
								:close-on-select="attribute.type === 'text'"
								:required="true"
								:options="attributes.find(attr => attr.id === attribute.id).values"
							></multiselect>
						</div>
					</div>

					{{-- varieties --}}
					<div v-show="!isVarietiesEmpty" class="table-responsive mt-5">
						<table class="table table-bordered text-nowrap text-center">
							<thead class="border-top">
								<tr>
									<th>عنوان</th>
									<th>قیمت (تومان)</th>
									<th>بارکد - SKU</th>
									<th>موجودی</th>
									<th>عملیات</th>
								</tr>
							</thead>
							<tbody>
								<template v-for="(attributes, index) in attributesCombinations">

									<tr>
										<td>
											<span v-for="(attr, index) in attributes.items" :key="index">
												<span v-text="attr.value" class="fs-12"></span>
												<span v-if="index !== attributes.items.length - 1">	- </span>
											</span>
										</td>
										<td>  
											<input 
												type="number" 
												class="form-control text-center"   
												placeholder="قیمت (تومان)"
												v-model="getVarietyValue(attributes.id).price"
											/>  
										</td>  
										<td>  
											<input 
												type="text" 
												class="mb-2 form-control text-center"   
												v-model="getVarietyValue(attributes.id).barcode"
												placeholder="بارکد"
												readonly
											/>  
											<input 
												type="text" 
												class="mt-2 form-control text-center"   
												v-model="getVarietyValue(attributes.id).SKU"
												placeholder="SKU"
											/>  
										</td>  
										<td>  
											<input 
												type="number" 
												class="form-control text-center"   
												v-model="getVarietyValue(attributes.id).quantity"
												placeholder="موجودی"
											/>  
										</td> 
										<td>
											<button 
												:data-target="'#variety-images' + attributes.id"
												data-toggle="modal"
												class="btn btn-sm btn-icon btn-success ml-1" 
												type="button">
												<i class="fa fa-image"></i>
											</button>
											<button 
												:data-target="'#variety-extra-details' + attributes.id"
												data-toggle="modal"
												class="btn btn-sm btn-icon btn-warning" 
												type="button">
												<i class="fa fa-pencil"></i>
											</button>
										</td>
									</tr>

									<div class="modal fade" :id="'variety-extra-details' + attributes.id" style="display: none" aria-hidden="true">
										<div class="modal-dialog modal-lg" role="document">
											<div class="modal-content modal-content-demo">
												<div class="modal-header">
													<p class="modal-title font-weight-bold">اطلاعات تنوع</p>
													<button aria-label="Close" class="close" data-dismiss="modal">
														<span aria-hidden="true">×</span>
													</button>
												</div>
												<div class="modal-body">

													<div class="row align-items-center my-3">
														<div class="col-xl-2">
															<label class="d-flex" :for="'variety-price' + attributes.id">قیمت (تومان) : <span class="text-danger">&starf;</span></label>
														</div>
														<div class="col-xl-10">
															<input 
																:id="'variety-price' + attributes.id" 
																type="text"
																class="form-control" 
																v-model="product.variety_values[attributes.id].price" 
																required
															/>
														</div>
													</div>

													<div class="row align-items-center my-3">
														<div class="col-xl-2">
															<label class="d-flex" :for="'variety-purchase-price' + attributes.id">قیمت خرید (تومان) :</label>
														</div>
														<div class="col-xl-10">
															<input 
																:id="'variety-purchase-price' + attributes.id" 
																type="text" 
																class="form-control" 
																v-model="product.variety_values[attributes.id].purchase_price"
															/>															
														</div>
													</div>

													<div class="row align-items-center my-3">
														<div class="col-xl-2">
															<label class="d-flex" :for="'variety-discount-type' + attributes.id">نوع تخفیف :</label>
														</div>
														<div class="col-xl-10">
															<multiselect
																dir="rtl"
																:id="'variety-discount-type' + attributes.id" 
																class="custom-multiselect"
																v-model="product.variety_values[attributes.id].discount_type"
																label="label"
																placeholder="انتخاب نوع تخفیف"
																:options="discountTypes"
															></multiselect>														
														</div>
													</div>

													<div class="row align-items-center my-3">
														<div class="col-xl-2">
															<label class="d-flex" :for="'variety-discount' + attributes.id">تخفیف :</label>
														</div>
														<div class="col-xl-10">
															<input 
																:id="'variety-discount' + attributes.id" 
																type="number" 
																class="form-control" 
																v-model="product.variety_values[attributes.id].discount"
															/>													
														</div>
													</div>

													<div class="row align-items-center my-3">
														<div class="col-xl-2">
															<label class="d-flex" :for="'variety-discount-until' + attributes.id">اتمام تخفیف :</label>
														</div>
														<div class="col-xl-10">
															<date-picker 
																:id="'variety-discount-until' + attributes.id" 
																v-model="product.variety_values[attributes.id].discount_until" 
																type="datetime" 
																format="YYYY-MM-DD HH:mm"
																display-format="jYYYY/jM/jD HH:mm"
															/>
														</div>
													</div>

													<div class="row align-items-center my-3">
														<div class="col-xl-2">
															<label class="d-flex" :for="'variety-barcode' + attributes.id">بارکد :</label>
														</div>
														<div class="col-xl-10">
															<input 
																:id="'variety-barcode' + attributes.id" 
																type="text" 
																class="form-control" 
																v-model="product.variety_values[attributes.id].barcode"
															/>
														</div>
													</div>

													<div class="row align-items-center my-3">
														<div class="col-xl-2">
															<label class="d-flex" :for="'variety-SKU' + attributes.id">SKU :</label>
														</div>
														<div class="col-xl-10">
															<input 
																:id="'variety-SKU' + attributes.id" 
																type="text" 
																class="form-control" 
																v-model="product.variety_values[attributes.id].SKU"
															/>
														</div>
													</div>

													<div class="row mt-3">
														<div class="col-12">
															<button class="btn-block btn btn-sm btn-danger" data-dismiss="modal">بستن</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="modal fade" :id="'variety-images' + attributes.id" style="display: none" aria-hidden="true">
										<div class="modal-dialog modal-lg" role="document">
											<div class="modal-content modal-content-demo">
												<div class="modal-header">
													<p class="modal-title font-weight-bold">تصاویر</p>
													<button aria-label="Close" class="close" data-dismiss="modal">
														<span aria-hidden="true">×</span>
													</button>
												</div>
												<div class="modal-body">

													<button
														type="button"
														class="btn btn-outline-primary"
														@click="triggerVarietiesFileInput(attributes.id)">
														افزودن عکس
													</button>

													<input
														type="file"
														:id="'variety-images-input' + attributes.id"
														multiple
														hidden
														accept="image/*"
														ref="fileInputs"
														@change="(e) => handleUploadVarietyImages(e, attributes.id)"
													/>

													<div class="row m-4">
														<div v-for="(image, imgIndex) in getVarietyValue(attributes.id).images" :key="imgIndex" class="position-relative col-12 col-xl-3 col-md-6 my-2">
															<img :src="image" alt="product-image" class="img-thumbnail image-size" style="width: 100%; height: auto;"/>
															<span class="remove-btn" @click="deleteVarietyImage(attributes.id, imgIndex)">&times;</span>
														</div>
													</div>
											
												</div>
											</div>
										</div>
									</div>

								</template>
							</tbody>
						</table>
					</div>

					{{-- set price for varieties modal --}}
					<div class="modal fade" id="set-price-for-varieties" style="display: none" aria-hidden="true">
						<div class="modal-dialog modal-md" role="document">
							<div class="modal-content modal-content-demo">
								<div class="modal-body">
									<div class="row">
										<h2 class="col-12 text-center">قیمت جدید را وارد کنید</h2>
										<div class="col-12">
											<input 
												type="number"
												placeholder="قیمت به تومان" 
												class="form-control"
												v-model="generalPriceForVarieties"
											/>
										</div>
									</div>
									<div class="row mt-3">
										<div class="col-12 d-flex justify-content-center">
											<button class="btn btn-sm btn-primary" data-dismiss="modal" @click="setGeneralPriceForVarieties">ثبت</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</x-slot>
			</x-card>

			{{-- specification --}}
			<x-card>
				<x-slot name="cardTitle">مشخصات محصول</x-slot>
				<x-slot name="cardBody">
					<div class="table-responsive mt-5">
						<table class="table table-bordered text-nowrap text-center">
							<thead class="border-top">
								<tr>
									<th style="width: 20%">نام</th>
									<th style="width: 80%">مقدار</th>
								</tr>
							</thead>
							<tbody>
								<template v-for="(specification, index) in specifications" :key="index">
									<tr>
										<td style="padding: 10px">
											<span class="fs-13" v-text="specification.label"></span>
											<span v-show="specification.required" class="text-danger"> &starf;</span>
											{{-- @{{ specification.label }} --}}
										</td>
										<td style="padding: 10px">
											<input 
												v-if="specification.type == 'text'"
												class="form-control" 
												type="text" 
												v-model="product.specifications[specification.id]"
											>
											<multiselect
												v-else
												dir="rtl"
												label="value"
												:options="specification.values"
												track-by="id"
												placeholder="انتخاب مقدار"
												:multiple="specification.type == 'multi_select'"
												:close-on-select="specification.type != 'multi_select'"
												:required="specification.required"
												class="custom-multiselect"
												v-model="product.specifications[specification.id]"
											></multiselect>
										</td>
									</tr>
								</template>
							</tbody>
						</table>
					</div>
				</x-slot>
			</x-card>

			{{-- sizechart --}}  
			<x-card>  
				<x-slot name="cardTitle">سایز چارت</x-slot>  
				<x-slot name="cardOptions">  
					<div class="card-options">  
						<button class="btn btn-sm btn-success" type="button" @click="addNewSizechart">افزودن سایز چارت</button>  
					</div>  
				</x-slot>  
				<x-slot name="cardBody">  
					<div v-for="(sizeChart, index) in product.size_charts" :key="index" class="row mb-5">  
						<div class="col-12 mb-4">  
							<button class="btn btn-danger btn-sm" type="button" @click="removeSizechart(index)">حذف سایز چارت</button>  
						</div>  
						<div class="col-12">  
							<div class="form-group">  
								<label :for="'size-chart-title-' + index">عنوان</label>  
								<input  
									:id="'size-chart-title-' + index"  
									type="text"  
									class="form-control"  
									placeholder="لطفا عنوان ساییز چارت را وارد کنید"  
									v-model="sizeChart.title"  
								/>  
							</div>  
						</div>  
						<div class="col-12">  
							<div class="form-group">  
								<label :for="'size-chart-type-' + index">نوع سایز چارت</label>  
								<multiselect  
									:id="'size-chart-type-' + index"  
									dir="rtl"  
									label="name"  
									track-by="id"  
									class="custom-multiselect"  
									placeholder="انتخاب نوع سایز چارت"  
									v-model="product.size_charts[index].choosenSizecharts"
									required  
									:options="sizeChartTypes"
									@select="(selectedSizeChartTypeObj) => addTypeAndChartToSizechart(selectedSizeChartTypeObj, index)"  
									@remove="(removedSizeChartTypeObj) => removeTypeAndChartFromSizechart(index)"  
								></multiselect>
							</div>  
						</div>  
						<div v-show="sizeChart.chart.length" class="col-12 mt-4">  
							<table class="table table-bordered text-nowrap text-center">  
								<tbody>  
									<template v-for="(charts, chartsIndex) in sizeChart.chart" :key="chartsIndex">  
										<tr>  
											<td v-for="(chartValue, chartValueIndex) in charts" :key="chartValueIndex">  
												<input 
													:disabled="chartsIndex === 0" 
													type="text" 
													class="form-control" 
													v-model="product.size_charts[index].chart[chartsIndex][chartValueIndex]"
												/>  
											</td>  
											<td>  
												<button type="button" @click="addNewChartInputRow(index)" class="btn btn-sm btn-icon btn-success">+</button>  
												<button  
													v-if="chartsIndex > 0"  
													type="button"  
													@click="removeChartInputRow(index, chartsIndex)"  
													:disabled="sizeChart.chart.length <= 2"  
													class="btn btn-sm btn-icon btn-danger mr-1">-</button>  
											</td>  
										</tr>  
									</template>  
								</tbody>  
							</table>  
						</div>  
					</div>  
				</x-slot>  
			</x-card>  

			<button class="btn btn-sm btn-primary" type="button" @click="storeProduct">ثبت محصول</button>

		</div>
		<div class="col-xl-4">

			{{-- publish --}}
			<x-card>
				<x-slot name="cardTitle">انتشار</x-slot>
				<x-slot name="cardOptions">
					<div class="card-options">
						<button class="btn btn-sm btn-primary" type="button" @click="storeProduct">ثبت محصول</button>
					</div>
				</x-slot>
				<x-slot name="cardBody">

					<div class="row align-items-center mb-2">
						<div class="col-xl-4">
							<label for="status-select">وضعیت<span class="text-danger">&starf;</span></label>
						</div>
						<div class="col-xl-8">
							<multiselect
								dir="rtl"
								id="status-select"
								class="custom-multiselect"
								v-model="product.status"
								label="label"
								placeholder="انتخاب وضعیت محصول"
								:options="productsStatuses"
								:select-label="null"
								:deselect-label="null"
								:selected-label="null"
								required
							></multiselect>
						</div>
					</div>

					<div class="row align-items-center mb-2">
						<div class="col-xl-4">
							<label for="status-select">زمان انتشار</label>
						</div>
						<div class="col-xl-8">
							<date-picker 
								id="published-at" 
								v-model="product.published_at" 
								type="datetime" 
								format="YYYY-MM-DD HH:mm"
								display-format="jYYYY/jM/jD HH:mm"
							/>
						</div>
					</div>

				</x-slot>
			</x-card>

			{{-- images --}}
			<x-card>
				<x-slot name="cardTitle">عکس‌ها</x-slot>
				<x-slot name="cardOptions">
					<div class="card-options">
						<button
							type="button"
							id="add-image-btn"
							class="btn btn-sm btn-outline-info"
							onclick="document.getElementById('product-images-input').click()"
						>افزودن عکس
						</button>
					</div>
				</x-slot>
				<x-slot name="cardBody">
					<div class="row">
						<input
							type="file"
							id="product-images-input"
							hidden
							multiple
							accept="image/*"
							@change="handleUploadImages"
						/>
					</div>
					<div class="row mt-3">
						<div v-for="(image, index) in product.images" :key="index" class="position-relative col-md-6 my-2">
							<img :src="image" alt="product-image" class="img-thumbnail image-size" style="width: 100%; height: auto;"/>
							<span class="remove-btn" @click="deleteImage(index)">&times;</span>
						</div>
					</div>
				</x-slot>
			</x-card>

			{{-- video --}}
			<x-card>
				<x-slot name="cardTitle">ویدیو محصول</x-slot>
				<x-slot name="cardBody">
					<div class="row align-items-center my-2">
						<div class="col-12">
							<div class="custom-file">
								<input 
									id="product-video-cover" 
									type="file" 
									class="custom-file-input"
									accept="image/*" 
									@change="handleUploadVideoCover"
								>
								<label class="custom-file-label">انتخاب کاور ویدیو</label>
							</div>
						</div>
						<div v-show="product.video_cover != null" class="position-relative col-12 mt-2 mb-5">
							<img :src="product.video_cover" class="img-thumbnail image-size" style="width: 100%; height: auto;"/>
							<span class="remove-btn" @click="deleteVideoCover">&times;</span>
						</div>
					</div>
					<div class="row align-items-center my-2">
						<div class="col-12">
							<div class="custom-file">
								<input 
									type="file" 
									id="product-video" 
									class="custom-file-input"
									accept="video/*"
									@change="handleUploadVideo"
								>
								<label class="custom-file-label">انتخاب ویدیو</label>
							</div>
						</div>
						<div v-show="product.video != null" class="position-relative col-12 mt-2">
							<video :src="product.video" controls></video>
							<span class="remove-btn" @click="deleteVideo">&times;</span>
						</div>
					</div>
				</x-slot>
			</x-card>
			
			{{-- pricing --}}
			<x-card>
				<x-slot name="cardTitle">قیمت گذاری</x-slot>
				<x-slot name="cardBody">

					{{-- unit price --}}
					<div class="row align-items-center mb-2">
						<div class="col-xl-4">
							<label for="product-unit-price">قیمت واحد</label>
						</div>
						<div class="col-xl-8">
							<input 
								id="product-unit-price" 
								type="number"
								v-model="product.unit_price" 
								class="form-control" 
								placeholder="قیمت را به تومان وارد کنید"
							>
						</div>
					</div>

					{{-- purchase price --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-4">
							<label for="product-purchase-price">قیمت خرید</label>
						</div>
						<div class="col-xl-8">
							<input id="product-purchase-price" v-model="product.purchase_price" type="number" class="form-control" placeholder="قیمت خرید را به تومان وارد کنید">
						</div>
					</div>

					{{-- discount type --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-4">
							<label for="product-discount-type">نوع تخفیف</label>
						</div>
						<div class="col-xl-8">
							<multiselect
								dir="rtl"
								id="product-discount-type" 
								class="custom-multiselect"
								label="label"
								track-by="name"
								placeholder="انتخاب نوع تخفیف"
								v-model="product.discount_type"
								:options="discountTypes"
								:select-label="null"
								:deselect-label="null"
								:selected-label="null"
							></multiselect>
						</div>
					</div>

					{{-- discount --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-4">
							<label for="product-discount">تخفیف</label>
						</div>
						<div class="col-xl-8">
							<input 
								id="product-discount" 
								type="number" 
								class="form-control" 
								v-model="product.discount"
								:max="product.discount_type?.name == 'percentage' ? 100 : ''"
								placeholder="مقدار تخفیف را وارد کنید"
							/>
						</div>
					</div>

					{{-- discount until --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-4">
							<label for="product-discount-until">زمان اتمام تخفیف</label>
						</div>
						<div class="col-xl-8">
							<date-picker 
								id="product-discount-until" 
								v-model="product.discount_until" 
								type="datetime" 
								format="YYYY/MM/DD HH:mm"
								display-format="jYYYY/jM/jD HH:mm"
							/>
						</div>
					</div>

					{{-- threshold quantity --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-4">
							<label for="product-threshold-quantity">تعداد آستانه</label>
						</div>
						<div class="col-xl-8">
							<input id="product-threshold-quantity" type="number" class="form-control" v-model="product.threshold_quantity"placeholder="تعداد آستانه را وارد کنید"/>
						</div>
					</div>

					{{-- threshold date --}}
					<div class="row align-items-center my-2">
						<div class="col-xl-4">
							<label for="product-threshold-date">تاریخ آستانه</label>
						</div>
						<div class="col-xl-8">
							<date-picker 
								id="product-threshold-date" 
								v-model="product.threshold_date" 
								type="datetime" 
								format="YYYY/MM/DD HH:mm"
								display-format="jYYYY/jM/jD HH:mm"
							/>
						</div>
					</div>

				</x-slot>
			</x-card>

			{{-- settings --}}
			<x-card>
				<x-slot name="cardTitle">تنظیمات</x-slot>
				<x-slot name="cardBody">
					<div class="row align-items-center mb-2">
						<div class="col-xl-4">
							<label for="product-low-stock-quantity-warning">اخطار موجودی <span class="text-danger">&starf;</span></label>
						</div>
						<div class="col-xl-8">
							<input id="product-low-stock-quantity-warning" type="number" v-model="product.low_stock_quantity_warning" class="form-control" required>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<label for="chargeable-checkbox" class="custom-control custom-checkbox">
								<input id="chargeable-checkbox" v-model="product.chargeable" type="checkbox" class="custom-control-input" value="1"/>
								<span class="custom-control-label">قابل شارژ</span>
							</label>
						</div>
						<div class="col-12">
							<label for="send-notif-to-customers" class="custom-control custom-checkbox">
								<input id="send-notif-to-customers" type="checkbox" class="custom-control-input" value="1"/>
								<span class="custom-control-label">ارسال نوتیفیکیشن به کاربران در انتظار</span>
							</label>
						</div>
						<div class="col-12">
							<label for="show-quantity" class="custom-control custom-checkbox">
								<input id="show-quantity" v-model="product.show_quantity" type="checkbox" class="custom-control-input" value="1"/>
								<span class="custom-control-label">مشاهده موجودی</span>
							</label>
						</div>
					</div>
				</x-slot>
			</x-card>

			{{-- SEO --}}
			<x-card>
				<x-slot name="cardTitle">اطلاعات سئو</x-slot>
				<x-slot name="cardOptions">
					<div class="card-options">
						<button class="btn btn-sm btn-primary" type="button" @click="storeProduct">ثبت محصول</button>
					</div>
				</x-slot>
				<x-slot name="cardBody">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="product-meta-title">عنوان متا</label>
								<input  id="product-meta-title" placeholder="عنوان متا" class="form-control" v-model="product.meta_title" type="text">
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="product-meta-description">توضیحات متا</label>
								<textarea id="product-meta-description" placeholder="توضیحات متا" class="form-control" v-model="product.meta_description" rows="4"></textarea>
							</div>
						</div>
					</div>
				</x-slot>
			</x-card>

		</div>
	</div>
</div>

@endsection

@section('scripts')

<script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>
<script src="{{ asset('assets/vue/multiselect/vue-multiselect.umd.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-jalaali@0.9.2/build/moment-jalaali.js"></script>
<script src="{{ asset('assets/vue/date-time-picker/vue3-persian-datetime-picker.umd.min.js') }}"></script>

<script>

  const {
    createApp
  } = Vue;

  createApp({
    components: {
      'multiselect': window['vue-multiselect'].default,
			'date-picker': Vue3PersianDatetimePicker,
    },
		mounted() {
			if (this.units.length) {
				this.product.unit = this.units[0];
			}
		},
    data() {
      return {
        message: "Hello Vue!",
        categories: @json($categories),
        attributes: @json($attributes),
        brands: @json($brands),
        units: @json($units),
        tags: @json($tags),
				specifications: @json($specifications),
				sizeChartTypes: @json($sizeChartTypes),
				productsStatuses: @json($productsStatuses),
				discountTypes: @json($discountTypes),
				generalPriceForVarieties: null,
				hasPublishedAt: false,
				choosenSizecharts: null,
        product: {
					title: null,
					quantity: null,
					SKU: null,
					barcode: null,
					image_alt: null,
					status: [],
					published_at: null,
					unit_price: null,
					purchase_price: null,
					discount_type: [],
					discount: null,
					discount_until: null,
					description: null,
					short_description: null,
					meta_description: null,
					meta_title: null,
					threshold_date: null,
					threshold_quantity: null,
					video: null,
					video_cover: null,
					chargeable: false,
					low_stock_quantity_warning: null,
					show_quantity: false,
          images: [],
          categories: [],
          attributes: [],
          attribute_values: {},
          specifications: {},
					variety_values: {},
					size_charts: [],
					tags: [],
          unit: [],
          brand: [],
        }
      };
    },
    methods: {
			showValidationError(errors) {  

				const list = document.createElement('ul');  
				list.className = 'list-group';

				for (const key in errors) {  
					if (errors.hasOwnProperty(key)) {  
						const errorsArray = errors[key];  
						errorsArray.forEach((errorMessage) => {  
							const listItem = document.createElement('li');  
							listItem.className = 'list-group-item';  
							listItem.textContent = errorMessage;
							list.appendChild(listItem); 
						});  
					}  
				}  

				Swal.fire({  
					title: "<b>خطا های زیر رخ داده است</b>",  
					html: list.outerHTML, 
					icon: "error",  
					confirmButtonText: "بستن",  
				});  
			},
			popup(type, title, message) {
				Swal.fire({
					title: title,
					text: message,
					icon: type,
					confirmButtonText: "بستن",
				});
			},
      addNewTag(value, attribute) {
        this.clearVarietyValues();
        this.product.attribute_values[attribute.id] = this.product.attribute_values[attribute.id] || [];
        const attribute_id = attribute.pivot.attribute_id;
        this.product.attribute_values[attribute.id].push({ value, attribute_id });
      },
      handleUploadImages(e) {
        const files = Array.from(e.target.files);
        files.forEach((file) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.product.images.push(e.target.result);
          };
          reader.readAsDataURL(file);
        });
      },
			handleUploadVideoCover(e) {  
        const videoCoverFile = e.target.files[0];  
        if (videoCoverFile) {  
					const reader = new FileReader();  
					reader.onload = (e) => {  
						this.product.video_cover = e.target.result;
					};  
					reader.readAsDataURL(videoCoverFile);  
        }  
			},  
			handleUploadVideo(e) {  
        const videoFile = e.target.files[0];  
        if (videoFile) {  
					const reader = new FileReader();  
					reader.onload = (e) => {  
						this.product.video = e.target.result;
					};  
					reader.readAsDataURL(videoFile);  
        }  
			},
      deleteImage(index) {
        this.product.images.splice(index, 1);
      },
			deleteVideo() {
        this.product.video = null;
      },
			deleteVideoCover() {
        this.product.video_cover = null;
      },
      getVarietyValue(attributeId) {
        if (!this.product.variety_values[attributeId]) {
          this.product.variety_values[attributeId] = {
            price: null,
						purchase_price: null,
            barcode: '',
            SKU: '',
            quantity: null,
            images: [],
						discount_type: '',
						discount: null,
						discount_until: null,
          };
          this.product.variety_values = { ...this.product.variety_values };
        }

        return this.product.variety_values[attributeId];
      },
      handleUploadVarietyImages(e, attributeId) {
        const files = Array.from(e.target.files);
        files.forEach((file) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.getVarietyValue(attributeId).images.push(e.target.result);
          };
          reader.readAsDataURL(file);
        });
      },
			deleteVarietyImage(attributeId, index) {
				this.product.variety_values[attributeId].images.splice(index, 1);
			},
			triggerVarietiesFileInput(attributeId) {  
				this.$refs.fileInputs.forEach(input => {  
					if (input.id === 'variety-images-input' + attributeId) {  
						input.click();
						return; 
					}  
				});  
			},  
      clearVarietyValues() {
        this.product.variety_values = {};
      },
			addNewSizechart() {  
				this.product.size_charts.push({  
					title: '',  
					type_id: null,  
					choosenSizecharts: {},
					chart: [],  
				});  
			},  
			addTypeAndChartToSizechart(selectedSizeChartTypeObj, index) {  
				const chartArr = ['سایزبندی', ...selectedSizeChartTypeObj.values.map(value => value.name)];  
				this.product.size_charts[index].type_id = selectedSizeChartTypeObj.id;  
				this.product.size_charts[index].chart = [chartArr];  
				this.addNewChartInputRow(index);
			},
			removeTypeAndChartFromSizechart(index) {
				this.product.size_charts[index].type_id = null;  
				this.product.size_charts[index].chart = [];  
			},
			addNewChartInputRow(sizeChartIndex) {  
				this.product.size_charts[sizeChartIndex].chart.push(Array(this.product.size_charts[sizeChartIndex].chart[0].length).fill(''));  
			},  
			removeChartInputRow(sizeChartIndex, chartIndex) {  
				this.product.size_charts[sizeChartIndex].chart.splice(chartIndex, 1);  
			},  
			removeSizechart(sizeChartIndex) {  
				this.product.size_charts.splice(sizeChartIndex, 1); 
			},  
			compileSpecifications() {

				if (Object.keys(this.product.specifications).length < 1) {
					return [];
				};
				
				const specifications = [];
				specifications.push(  
					...Object.entries(this.product.specifications).map(([specificationId, specificationValueObjects]) => {  
						const specification = this.specifications.find(s => s.id == specificationId);

						let value;
						if (specification.type == 'text') {
							value = specificationValueObjects;
						}else {
							value = specification.type == 'multi_select'  
							? specificationValueObjects.map(specificationValue => specificationValue.id)   
							: specificationValueObjects.id;
						}

						return {  
							id: specificationId,  
							value: value  
						};  
					})
				);  

				return specifications;
			},
			setGeneralPriceForVarieties() {
				if (this.generalPriceForVarieties > 0) {
					Object.values(this.product.variety_values)?.forEach(variety => variety.price = this.generalPriceForVarieties);			
				}
			},
			async storeProduct() {

				const clonedProduct = JSON.parse(JSON.stringify(this.product));

				clonedProduct.categories = this.product.categories?.map(category => category.id) || [];
				clonedProduct.brand_id = this.product.brand?.id || null;
				clonedProduct.unit_id = this.product.unit?.id || null;
				clonedProduct.tags = this.product.tags?.map(tag => tag.id) || [];
				clonedProduct.specifications = this.compileSpecifications();
				clonedProduct.status = this.product.status?.name || null;
				clonedProduct.discount_type = this.product.discount_type?.name || null;
				clonedProduct.varieties = this.varieties;

				delete clonedProduct.brand;
				delete clonedProduct.unit;
				delete clonedProduct.attribute_values;
				delete clonedProduct.variety_values;
				delete clonedProduct.attributes;

				try {
					const response = await fetch(@json(route('admin.products.store')), {
						method: 'POST',
						headers: {
							'Accept': 'application/json',
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': @json(csrf_token())
						},
						body: JSON.stringify({ product: clonedProduct }),
					});
					
					const result = await response.json();

					if (!response.ok) {
            switch (response.status) {
              case 422:
                this.showValidationError(result.errors);
                break;
              case 404:
                this.popup('error', 'خطای 404', 'چنین چیزی وجود ندارد');
                break;
              case 500:
                this.popup('error', 'خطای سرور', result.message);
                break;
            }
            return;
          }

					if (response.ok && response.status == 200) {
						this.popup('success', 'عملیات موفق', result.message);
						window.location.replace(@json(route('admin.products.index')));
					} else {
						this.popup('error', 'خطا', 'لطفا با پشتیبانی تماس بگیرید');
					}

				} catch (error) {
					console.error('There was a problem with the submission:', error);
				}

			},
		},
    computed: {
      uniqueAttributes() {
        const selectedCategoriesAttributes = this.product.categories
          .map((category) => category.attributes)
          .flat();

        // Remove duplicate attributes
        const uniqueAttributes = selectedCategoriesAttributes.filter(
          (attribute, index, self) => index === self.findIndex(
            (t) => t.id === attribute.id && t.title === attribute.title
          )
        );

        return uniqueAttributes;
      },
      attributesCombinations() {
        const attributes = Object.values(this.product.attribute_values).filter(
          (values) => values.length > 0
        );
        // all possible combinations into this format
        /**
         * [
         *    { id: random, items: [{ id: 1, value: 'red' }, { id: 2, value: 'small' } ],
         * ]
         *
         *
        */
        const mix = attributes.reduce((acc, curr) => {
          return acc.flatMap(a => curr.map(b => [...a, b]));
        }, [[]]);

        return mix.length > 0 && mix[0].length > 0
          ? mix.map(combination => ({
              id: Math.random().toString(36).substr(2, 9),
              items: combination
          }))
          : [];
      },
			varieties() {

				if (Object.keys(this.product.variety_values).length === 0) {
					return [];
				}

        return [
          ...Object.entries(this.product.variety_values).map(([varietyId, variety]) => {
            return {
              price: variety.price,
              purchase_price: variety.purchase_price,
              barcode: variety.barcode,
              SKU: variety.SKU,
							color_id: null,
              quantity: variety.quantity,
              discount_type: variety.discount_type?.name || null,
							discount_until: variety.discount_until,
              discount: variety.discount,
              images: variety.images,
              attributes: this.attributesCombinations.find(
                (combination) => combination.id === varietyId
              ).items.map((item) => ({
								id: item.attribute_id,
								value: item.hasOwnProperty('id') ? item.id :item.value // if attribute type is select_box the value should be the attribute_value_id
              }))
            }
          })
        ];
      },
			isVarietiesEmpty() {
				return Object.keys(this.product.variety_values).length === 0;
			},
    }
  }).mount("#app");
</script>

@endsection

@section('styles')

<link rel="stylesheet" href="{{ asset('assets/vue/multiselect/vue-multiselect.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/vue/multiselect/custom-styles.css') }}"/>

<style>

	label {
		font-size: 12px;
	}

	input {
		font-size: 12px !important;
	}

	#published-at-input-group {  
    transition: opacity 0.5s ease, max-height 0.5s ease;  
    overflow: hidden;   
	}  

	.hide-published-at-input {  
    opacity: 0;  
    max-height: 0;  
	}  

	.image-size {  
		width: 100%;         
		min-height: 100px;       
		max-height: 100px;       
		object-fit: cover;   
	}  

	.remove-btn {
		position: absolute;
		top: 5px;
		right: 5px;
		color: red;
		font-size: 20px;
		cursor: pointer;
	}

	.vpd-icon-btn {
		margin-bottom: 0;
	}

</style>

@endsection 
