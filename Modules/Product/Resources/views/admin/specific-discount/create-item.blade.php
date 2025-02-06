@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
		<x-breadcrumb :items="[
      ['title' => 'لیست تخفیفات ویژه', 'route_link' => 'admin.specific-discounts.index'],
      ['title' => 'انواع تخفیفات', 'route_link' => 'admin.specific-discounts.types.index', 'parameter' => $specificDiscountType->specific_discount],
      ['title' => 'آیتم های تخفیف', 'route_link' => 'admin.specific-discounts.items.index', 'parameter' => $specificDiscountType],
      ['title' => 'ایجاد آیتم جدید']
    ]" />
  </div>

  <form 
    id="UpdateItemsForm"
    action="{{ route('admin.specific-discounts.items.store', $specificDiscountType) }}"
    method="POST"
    class="d-none">
    @csrf
  </form>

  <x-card>
    <x-slot name="cardTitle">ثبت آیتم جدید تخفیف ویژه</x-slot>
    <x-slot name="cardOptions"><x-card-options /></x-slot>
    <x-slot name="cardBody">

      <div class="row">
        <div class="col-12 col-lg-6 col-xl-3">
          <div class="form-group">
            <label for="type-select-box">انتخاب گروه تخفیف</label>
            <select id="type-select-box" class="form-control" name="type">
              <option value=""></option>
              @foreach ($itemTypes as $type)
                <option value="{{ $type }}">{{ config('product.specificDiscountItemTypes.' . $type) }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div id="specific-discount-row" class="row p-4" style="background-color: #cb9864; border-radius: 24px;">  

        <div class="col-12" id="discount-item-type-section-product">  
          <div class="row">  
            <div class="col-12 col-xl-3 form-group">  
              <label for="product-select-box">محصول</label>  
              <select id="product-select-box" class="form-control">  
                <option value="">انتخاب محصول</option>  
              </select>  
            </div>  
          </div>  
          <div class="row mt-5 product-list-row"></div>  
        </div> 

        <div class="col-12" id="discount-item-type-section-category">  
          <div class="row">  
            <div class="col-12 col-xl-3 form-group">  
              <label for="category-select-box">انتخاب دسته بندی</label>  
              <select id="category-select-box" class="form-control">  
                <option value="">انتخاب</option>  
                @foreach ($categories as $category)  
                  <option value="{{ $category->id }}">{{ $category->title }}</option>  
                @endforeach  
              </select>  
            </div>  
          </div>  
          <div class="row mt-5 category-list-row"></div>  
        </div>  

        <div class="col-12" id="discount-item-type-section-range">  
          <div class="row">  
            <div class="col-12 col-xl-6">
              <div class="form-group">
                <label>قیمت از (تومان) :</label>  
                <input type="text" class="form-control comma range-from-input">  
              </div>  
            </div>  
            <div class="col-12 col-xl-6">
              <div class="form-group">
                <label>قیمت تا (تومان) :</label>  
                <input type="text" class="form-control comma range-to-input">  
              </div>  
            </div>  
          </div>  
        </div>  

        <div class="col-12" id="discount-item-type-section-balance">  
          <div class="row">  
            <div class="col-12 col-xl-6 form-group">  
              <label>میزان موجودی :</label>  
              <input type="text" class="form-control balance-input">  
            </div>  
            <div class="col-12 col-xl-6 form-group">  
              <label for="balance-type-select-box">نوع موجودی :</label>  
              <select id="balance-type-select-box" class="form-control">  
                <option value="">انتخاب</option>  
                <option value="more">بیشتر</option>  
                <option value="less">کمتر</option>  
              </select>  
            </div>  
          </div>  
        </div>  

      </div>  
    </x-slot>
  </x-card>

  <div class="row" style="margin-bottom: 150px;">
    <div class="col-12">
      <div class="d-flex justify-content-center">
        <button id="submit-items-button" type="button" class="btn btn-sm btn-primary">ثبت آیتم های تخفیف ویژه</button>
      </div>
    </div>
  </div>

  <div id="Examples">

    <div id="example-discount-item-type-product" class="col-2 my-2 product-list-item">
      <div class="d-flex align-items-center">
        <button
          onclick="removeProductFromDiscountItem(event)"
          data-product-id="" 
          class="btn btn-sm btn-danger btn-icon text-white" 
          style="border-bottom-left-radius: 0; border-top-left-radius: 0;">
          <i class="fa fa-trash"></i>
        </button>
        <span class="btn btn-sm btn-icon btn-success product-title-span" style="border-bottom-right-radius: 0; border-top-right-radius: 0;"></span>
      </div>
    </div>

    <div id="example-discount-item-type-category" class="col-2 my-2 category-list-item">  
      <div class="d-flex align-items-center">  
        <button  
          onclick="removeCategoryFromDiscountItem(event)"  
          data-category-id=""   
          class="btn btn-sm btn-danger btn-icon text-white"   
          style="border-bottom-left-radius: 0; border-top-left-radius: 0;">  
          <i class="fa fa-trash"></i>  
        </button>  
        <span class="btn btn-sm btn-icon btn-success category-title-span" style="border-bottom-right-radius: 0; border-top-right-radius: 0;"></span>  
      </div>  
    </div>  

  </div>

@endsection

@section('scripts')
    <script>

      const specificDiscountRow = $('#specific-discount-row');
      const productSelectBox = $('#product-select-box');
      const categorySelectBox = $('#category-select-box');
      const typeSelectBox = $('#type-select-box');
      const balanceTypeSelectBox = $('#balance-type-select-box');

      const exampleProductDiscountItem = $('#example-discount-item-type-product').clone().removeAttr('id');
      const exampleCategoryDiscountItem = $('#example-discount-item-type-category').clone().removeAttr('id');

      const allCategories = @json($categories);
      const productsCollection = [];

      let deleteExampleElements = () => $('#Examples').remove();
      let makeSelectBoxLabel = (element, label) => element.select2({placeholder: label});
      let hideSpecificDiscountRow = () => specificDiscountRow.hide();
      let showSpecificDiscountRow = () => specificDiscountRow.show();
      let removeProductFromDiscountItem = (event) => $(event.target).closest('.product-list-item').remove();
      let removeCategoryFromDiscountItem = (event) => $(event.target).closest('.category-list-item').remove();

      class DiscountItemsInput {  

        constructor(inputName, inputValue) {  
          this.name = inputName;  
          this.value = inputValue;  
          this.makeInput();  
        }  

        makeInput() {
          let input = $('<input type="hidden" name="" value="" />');
          input.attr('name', this.name);
          input.attr('value', this.value);
          $('#UpdateItemsForm').append(input);
        }

      }  

      function makeSelectBoxLabels() {
        makeSelectBoxLabel(productSelectBox, 'جستجوی محصول');
        makeSelectBoxLabel(categorySelectBox, 'انتخاب دسته بندی');
        makeSelectBoxLabel(typeSelectBox, 'انتخاب گروه تخفیف');
        makeSelectBoxLabel(balanceTypeSelectBox, 'انتخاب نوع موجودی');
      }

      function searchProducts() {  
        productSelectBox.select2({  
          ajax: {  
            url: @json(route('admin.products.search')),  
            dataType: 'json',  
            delay: 250, 
            processResults: (response) => {  
              let products = response.data.products || [];  
              products.forEach(product => {  
                if (!productsCollection?.find(p => p.id === product.id)) {  
                  productsCollection.push(product);  
                }  
              });  
              return {  
                results: products.map(product => ({  
                  id: product.id,  
                  title: product.title,  
                })),  
              };  
            },  
            cache: true,  
            error: (jqXHR, textStatus, errorThrown) => {  
              console.error("Error fetching products:", textStatus, errorThrown);  
            },  
          },  
          placeholder: 'عنوان محصول را وارد کنید',  
          minimumInputLength: 1,  
          templateResult: (repo) => {  
            if (repo.loading) return "در حال بارگذاری...";  

            let $container = $(  
              "<div class='select2-result-repository clearfix'>" +  
              "<div class='select2-result-repository__meta'>" +  
              "<div class='select2-result-repository__title'></div>" +  
              "</div>" +  
              "</div>"  
            );  

            $container.find(".select2-result-repository__title").text(repo.title);  

            return $container;  
          },  
          templateSelection: (repo) => {  
            return repo.id ? repo.title : repo.text;  
          },  
        });  
      }

      function hideSpecificDiscountRowChildren() {
        specificDiscountRow.children().each(function () {
          $(this).hide();
        });
      }

      function showSpecificDiscountRowChildren() {
        typeSelectBox.change(() => {
          hideSpecificDiscountRowChildren();
          showSpecificDiscountRow();
          let discountItemType = typeSelectBox.val();
          $('#discount-item-type-section-' + discountItemType).show();
        });
      }

      function productSelectBoxChangeEvent() {
        productSelectBox.change(() => {

          let productId = productSelectBox.val();
          let isProductOrVarietyExists = specificDiscountRow.find('.product-list-item').filter(function() {
            return $(this).find('button').data('product-id') == productId;
          });

          if (isProductOrVarietyExists.length < 1) {

            let productListItem = exampleProductDiscountItem.clone();
            let btn = productListItem.find('button');
            let span = productListItem.find('span'); 
            let product = productsCollection.find(p => p.id == productId);

            btn.attr('data-product-id', product.id);
            span.text(product.title);
            specificDiscountRow.find('.product-list-row').append(productListItem);

          }
        });
      }

      function categorySelectBoxChangeEvent() {
        categorySelectBox.on('select2:select', () => {
          let categoryId = categorySelectBox.val();
          let isCategoryExists = specificDiscountRow.find('.category-list-item').filter(function() {
            return $(this).find('button').data('category-id') == categoryId;
          });

          if (isCategoryExists.length < 1) {

            let categoryListItem = exampleCategoryDiscountItem.clone();
            let btn = categoryListItem.find('button');
            let span = categoryListItem.find('span');
            let category = allCategories.find(c => c.id == categoryId);

            btn.attr('data-category-id', category.id);
            span.text(category.title);
            specificDiscountRow.find('.category-list-row').append(categoryListItem);

          }
        });
      }

      function storeItems() {  
        $('#submit-items-button').click(() => {  
          const discountType = typeSelectBox.val();  

          const addInput = (name, value) => {  
            if (value) {  
              new DiscountItemsInput(name, value);  
            }  
          };  
          addInput('type', discountType);
          switch (discountType) {  
            case 'product':  
            case 'category': {  
              const selector = discountType === 'product' ? '.product-list-item' : '.category-list-item';  
              const ids = $(selector).map(function () {  
                return $(this).find('button').data(discountType === 'product' ? 'product-id' : 'category-id');  
              }).get().join(','); 
              addInput('model_ids', ids);
              break;  
            }  
            case 'balance': {  
              const balance = specificDiscountRow.find('.balance-input').val()?.replace(/,/g, "");  
              const balanceType = specificDiscountRow.find('#balance-type-select-box').val();  
              addInput('balance', balance);  
              addInput('balance_type', balanceType);  
              break;  
            }  
            case 'range': {  
              const rangeFrom = specificDiscountRow.find('.range-from-input').val()?.replace(/,/g, "");  
              const rangeTo = specificDiscountRow.find('.range-to-input').val()?.replace(/,/g, "");  
              addInput('range_from', rangeFrom);  
              addInput('range_to', rangeTo);  
              break;  
            }  
          }  

          $('#UpdateItemsForm').submit();  
        });  
      }  

      makeSelectBoxLabels();
      searchProducts();
      
      $(document).ready(() => {
        hideSpecificDiscountRow();
        hideSpecificDiscountRowChildren();
        showSpecificDiscountRowChildren();
        productSelectBoxChangeEvent();
        categorySelectBoxChangeEvent();
        deleteExampleElements();
        storeItems();
      });

    </script>
@endsection