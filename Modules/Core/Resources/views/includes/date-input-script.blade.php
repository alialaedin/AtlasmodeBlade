<script>
  $('#' + "{{ $textInputId }}").MdPersianDateTimePicker({
    targetDateSelector: '#' + "{{ $dateInputId }}",
    targetTextSelector: '#' + "{{ $textInputId }}",
    englishNumber: false,
    toDate:true,
    enableTimePicker: true,
    dateFormat: 'yyyy-MM-dd HH:mm',
    textFormat: 'yyyy-MM-dd HH:mm',
    groupId: 'rangeSelector1',
  });
</script>
