<script>

    $('#{{ $productInputId }}').select2({
        placeholder: 'انتخاب محصول'
    });
    $('#{{ $varietyInputId }}').select2({
        placeholder: 'ابتدا محصول را انتخاب کنید سپس تنوع'
    });

    $('#{{ $productInputId }}').change(() => {
        $.ajax({
            url: '{{ route('admin.stores.load-varieties') }}',
            type: 'POST',
            data: {
                product_id: $('#{{ $productInputId }}').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (Array.isArray(response.varieties) && response.varieties.length > 0) {
                    let options = '<option value="">انتخاب</option>';

                    let url = window.location.href;
                    let parsedUrl = new URL(url);
                    let params = new URLSearchParams(parsedUrl.search);

                    const selectedVarietyId = params.get('variety_id');

                    response.varieties.forEach((variety) => {
                        options += `<option value="${variety.id}" ${selectedVarietyId === String(variety.id) ? 'selected' : ''}>${variety.title_showcase.fullTitle}</option>`;
                    });
                    $('#{{ $varietyInputId }}').html(options).trigger('change');

                } else {
                    console.error('Expected varieties array, but got:', response.varieties);
                }
            }
        });
    });
</script>
