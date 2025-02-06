<textarea
    class="ckeditor form-control"
    id="{{$editor_id ?? 'ckeditor'}}"
    {{isset($required) ? 'required' : ''}}
    name="{{$name}}">
    {{(str_contains(Request::path(), 'edit') ? $model->$name : old($name))}}
</textarea>


<script src="{{asset('/assets/editor/ckeditor/ckeditor.js')}}"></script>
<script>
    var options = {
        filebrowserImageBrowseUrl: "{{asset('/assets/editor/ckfinder/ckfinder.html?type=Images')}}",
        filebrowserImageUploadUrl: "{{asset('/assets/editor/ckfinder/ckfinder.html?type=Images')}}",
        filebrowserBrowseUrl: "{{asset('/assets/editor/ckfinder/ckfinder.html?type=Images')}}",
        filebrowserUploadUrl: "{{asset('/assets/editor/ckfinder/ckfinder.html?type=Images')}}"
    };
    CKEDITOR.replace({{$editor_id ?? 'ckeditor'}}, options);
</script>
