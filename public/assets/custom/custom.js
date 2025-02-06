function confirmDelete(formId) {  
  Swal.fire({  
    title: "آیا مطمئن هستید؟",  
    text: "بعد از حذف این آیتم دیگر قابل بازیابی نخواهد بود!",  
    icon: "warning",  
    showCancelButton: true,  
    showCloseButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "حذف کن",  
    cancelButtonText: "انصراف",   
    dangerMode: true,  
  }).then((result) => {  
    if (result.isConfirmed) {  
      document.getElementById(formId).submit();  
      Swal.fire("آیتم با موفقیت حذف شد!", {  
        icon: "success",  
      });  
    }  
  });  
} 

function comma() {
  $("input.comma").on("keyup", function (event) {
    if (event.which >= 37 && event.which <= 40) return;
    $(this).val(function (index, value) {
      return value
        .replace(/\D/g, "")
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    });
  });
}

// 200 تا آرایه بده همه رو در هم به ضورت ماتریکسی ضرب میکنه
function crossJoin(...arrays) {  
  return arrays.reduce((acc, arr) => {  
    return acc.flatMap(a => arr.map(b => [...a, b]));  
  }, [[]]);  
}  

$(document).ready(function () {
  comma();
});
