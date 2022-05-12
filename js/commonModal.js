$(document).on("click","#historyList tr,.customerEdit",function () {
    customerModalLaunch(
      $(this).data("customer-id"),
      $(this).data("customer-num")
    );
  }
);
$(document).on("click",".usageAdd,.usageEdit",function () {
  usageModalLaunch(
    $(this).data("cid"),
    $(this).data("uid"),
  );
});
function customerModalLaunch(id, num, mode) {
  if(id == undefined || id == null){
    id = '';
  }
  if(num == undefined || num == null){
    num = '';
  }
  if(mode == undefined || mode == null){
    mode = '';
  }
  window.open(
    "customerData.php?cid=" + id + "&num=" + num + "&mode=" + mode,
    "window_cid" + num,
    "width=870,height=760,scrollbars=yes"
  );
}
function usageModalLaunch(cid,uid) {
  window.open(
    "usageData.php?cid=" + cid + "&uid=" + uid,
    "window_cid_usage_" + cid + uid,
    "width=870,height=500,scrollbars=yes"
  );
}
$(".timePicker").timepicker();
$("#startDate,#endDate,.startDate").datepicker({ dateFormat: "yy年mm月dd日" });

$(document).on("change", ".colEdit", function () {
  $this = $(this);
  var id = $this.closest('.dataBox').data("id");
  var mode = $this.closest('.dataBox').data("mode");
  var col = $this.data("col");
  var val = $this.val();
  if (col == "+iZpXw9LMtKdSxy7TqrLWw==") {
    val = $('[class="colEdit data-' + id + '"]:checked')
      .map(function () {
        return $(this).val();
      }).get();
  }
  $.ajax({
    url: "ajax/colEdit.php",
    type: "POST",
    data: {
      id: id,
      mode: mode,
      col: col,
      val: val,
    },
    cache: false,
  })
  .done(function (data) {
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
  });
});
$('.girlSelector').select2();
$('.shopSelector').select2();

$(document).on({
    mouseenter: function() {
      $(this).removeClass('current');
    }
}, "#historyList tbody tr.current")

$(document).on("change", ".checkAll", function () {
  $('#' + $(this).data('id') + ' tbody input').prop('checked',$(this).prop('checked'));
});

$(document).on("click", ".dataDelete", function () {
  $t = $(this);
  Swal.fire({
    title: "削除確認",
    text:
      "選択したデータを削除して宜しいですか？",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#91dbff",
    cancelButtonColor: "#ffa591",
    confirmButtonText: "削除",
    cancelButtonText: "キャンセル",
  }).then((result) => {
    if (result.isConfirmed) {
      $('#' + $(this).data('id') + ' tbody input:checked').map(function () {
        $(this).parents('tr').remove();
        var id = $(this).val();
        var mode = $t.data('mode');
        $.ajax({
          url: "ajax/dataDelete.php",
          type: "POST",
          data: {
            id: id,
            mode: mode,
          },
          cache: false,
        })
        .done(function (data) {
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
        });
      }).get();
    }
  });
});
$(document).on("click", "#navi a , .searchBtn", function () {
  $(this).css('pointer-events','none');
  $('.loading').show();
});
let closeTime = 300000;//300sec=5min
let timer;
let initializeFormData = JSON.stringify($('form').serializeArray());
$(document).on("click change", "input,textarea,select", function () {
  clearTimeout(timer);
  timer = setTimeout(function(){
    let currentFormData = JSON.stringify($('form').serializeArray());
    if(initializeFormData == currentFormData){
      open('about:blank', '_self').close();
    } else {
      if(window.confirm('データが保存されていませんがウィンドウを閉じてよろしいですか？')){
        open('about:blank', '_self').close();
      } else {
        $('input[type="text"]').trigger("click");
      }
    }
  }, closeTime);
});

timer = setTimeout(function(){
  open('about:blank', '_self').close();
}, closeTime);