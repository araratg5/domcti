$(document).on("click","#latestList tbody tr,#historyList tbody tr,.customerEdit",function () {
    customerModalLaunch(
      $(this).data("customer-id"),
      $(this).data("customer-num"),
      0
    );
  }
);
$(document).on("click",".usageAdd,.usageEdit",function () {
  usageModalLaunch(
    $(this).data("cid"),
    $(this).data("uid"),
  );
});

let showingNumberListjson;
let showingNumberListArray = [];
let showingNumberCount = 1;
let clickCount = 0;
function customerModalLaunch(id, num, isCall) {
  clickCount++;
  if(id == undefined || id == null){
    id = '';
  }
  if(num == undefined || num == null){
    num = '';
  }
  if(isCall == undefined || isCall == null){
    isCall = '';
  }
  showingNumberListjson = localStorage.getItem('showing_number_list');
  if(showingNumberListjson){
    showingNumberListArray = JSON.parse(showingNumberListjson);
  }
  showingNumberCount = showingNumberListArray.length + 1;
  
  let screenWidth=parseInt(screen.availWidth);
  var leftPos=screenWidth-870;
  var topPos = showingNumberCount * 30;
  if(!showingNumberListArray.filter(e => e == num).length){
    window.open(
      "customerData.php?cid=" + id + "&num=" + num + "&isCall=" + isCall,
      "window_cid" + num,
      "width=870,height=760,scrollbars=yes,top=" + topPos + "px,left=" + leftPos + "px"
    );
    showingNumberListArray.push(num);
    showingNumberListJson = JSON.stringify(showingNumberListArray, undefined, 1);
    localStorage.setItem('showing_number_list', showingNumberListJson);
    clickCount = 0;
  }
  if(clickCount == 3){
    var topPos = 0;
    window.open(
      "customerData.php?cid=" + id + "&num=" + num + "&isCall=" + isCall,
      "window_cid" + num,
      "width=870,height=760,scrollbars=yes,top=" + topPos + "px,left=" + leftPos + "px"
    );
  }
}
function usageModalLaunch(cid,uid) {
  if(cid == undefined || cid == null){
    cid = '';
  }
  if(uid == undefined || uid == null){
    uid = '';
  }
  window.open(
    "usageData.php?cid=" + cid + "&uid=" + uid,
    "window_cid_usage_" + cid + uid,
    "width=870,height=500,scrollbars=yes"
  );
}
$(".timePicker").timepicker();
$("#startDate,#endDate,.startDate").datepicker({ dateFormat: "yy???mm???dd???" });
function modalClose() {
  let mode = $('#mode').val()
  $('.loading').show();
  switch(mode){
    case 'top':
      $.ajax({
        url: "ajax/callUpdate.php",
        type: "POST",
        cache: false,
      })
      .done(function (data) {
        $('#latestTableWrapper').html(data);
        $('.loading').hide();
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
      });
      break;
    case 'history':
      $.ajax({
        url: "ajax/historyUpdate.php",
        type: "POST",
        cache: false,
      })
      .done(function (data) {
        $('#historyTableWrapper').html(data);
        $('.loading').hide();
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
      });
      break;
    case 'customer':
      $.ajax({
        url: "ajax/customerUpdate.php",
        type: "POST",
        cache: false,
      })
      .done(function (data) {
        $('#customerTableWrapper').html(data);
        $('.loading').hide();
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
      });
      break;
    case 'usage':
      $.ajax({
        url: "ajax/usageUpdate.php",
        type: "POST",
        cache: false,
      })
      .done(function (data) {
        $('#usageTableWrapper').html(data);
        $('.loading').hide();
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
      });
      break;
      
  }
}
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
    title: "????????????",
    text:
      "?????????????????????????????????????????????????????????",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#91dbff",
    cancelButtonColor: "#ffa591",
    confirmButtonText: "??????",
    cancelButtonText: "???????????????",
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
          modalClose()
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
$(document).on("click", "#latestList td", function () {
  $('#latestList tr').removeClass('current');
  $(this).parent('tr').addClass('current');
});
$(document).on("click", "#latestList tbody input,#historyList tbody input", function () {
  e.preventDefault();
});
$(document).on("click", "#historyList td", function () {
  $('#historyList tr').removeClass('current');
  $(this).parent('tr').addClass('current');
});
$(document).on("click", ".usageEdit", function () {
  $('#usageList tr').removeClass('current');
  $(this).parents('tr').addClass('current');
});
$(document).on("click", ".customerEdit", function () {
  $('#customerList tr').removeClass('current');
  $(this).parents('tr').addClass('current');
});
$(document).on("change","input[name='all_period']",function () {
  if($(this).prop('checked')){
    $("[name='start_date'],[name='start_time'],[name='end_date'],[name='end_time']").attr('readonly',true);
  } else {
    $("[name='start_date'],[name='start_time'],[name='end_date'],[name='end_time']").attr('readonly',false);
  }
});