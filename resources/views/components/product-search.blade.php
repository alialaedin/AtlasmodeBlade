@props([
  'cols',
  'productInputName' => 'product_id',
  'varietyInputName' => 'variety_id',
  'productInputId' => 'product-select-box',
  'varietyInputId' => 'variety-select-box',
  'hasLabel' => true
])

<div class="{{ $cols }}">
  <div class="form-group">
    @if ($hasLabel)
      <label>انتخاب محصول :</label>
    @endif
    <select id="{{ $productInputId }}" name="{{ $productInputName }}" class="form-control">
      <option value=""></option>
    </select>
  </div>
</div>

<div class="{{ $cols }}">
  <div class="form-group">
    @if ($hasLabel)
      <label>انتخاب تنوع :</label>
    @endif
    <select id="{{ $varietyInputId }}" name="{{ $varietyInputName }}" class="form-control">
      <option value=""></option>
    </select>
  </div>
</div>

@push('ProductSearchScripts')
  <script>

    $('#' + @json($varietyInputId)).select2({ placeholder: 'ابتدا محصول را جستجو کنید' });
    $('#' + @json($productInputId)).select2({  
      ajax: {  
        url: @json(route('admin.products.search')),  
        dataType: 'json',  
        delay: 250, 
        processResults: (response) => {  
          let products = response.data.products || [];  
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

        let container = $(  
          "<div class='select2-result-repository clearfix'>" +  
          "<div class='select2-result-repository__meta'>" +  
          "<div class='select2-result-repository__title'></div>" +  
          "</div>" +  
          "</div>"  
        );  

        container.find(".select2-result-repository__title").text(repo.title);  

        return container;  
      },  
      templateSelection: (repo) => {  
        return repo.id ? repo.title : repo.text;  
      },  
    });  

    $('#' + @json($productInputId)).on('select2:select', () => {
      $.ajax({
        url: @json(route('admin.products.load-varieties')),
        type: 'GET',
        data: {
          product_id: $('#' + @json($productInputId)).val()
        },
        success: function(response) {

          if (Array.isArray(response.varieties) && response.varieties.length > 0) {

            const url = window.location.href;
            const parsedUrl = new URL(url);
            const params = new URLSearchParams(parsedUrl.search);
            const selectedVarietyId = params.get('variety_id');

            let options = '<option value="">انتخاب</option>';
            response.varieties.forEach((variety) => {
              const title = variety.title + ' |  موجودی: ' + variety.quantity;
              const selected = selectedVarietyId === String(variety.id) ? 'selected' : '';
              options += `<option ${selected} value="${variety.id}">${title}</option>`;
            });

            $('#' + @json($varietyInputId)).html(options);
            $('#' + @json($varietyInputId)).select2({ placeholder: 'تنوع را انتخاب کنید' });
            
          }
        }
      });
    });

  </script>
@endpush