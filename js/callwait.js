function customerModalLaunch(id, num) {
  if(id == undefined || id == null){
    id = '';
  }
  if(num == undefined || num == null){
    num = '';
  }
  window.open(
    "customerData.php?cid=" + id + "&num=" + num,
    "window_cid" + num,
    "width=870,height=760,scrollbars=yes"
  );
}
function usageModalLaunch(cid,uid) {
  window.open(
    "usageData.php?cid=" + cid + "&uid=" + uid,
    "window_cid_usage_" + cid + uid,
    "width=870,height=300,scrollbars=yes"
  );
}

function parentReload(mode) {
  //location.reload();
  $.ajax({
    url: "ajax/callHistoryUpdate.php",
    type: "POST",
    cache: false,
  })
  .done(function (data) {
    $('#callHistoryTableWrapper').html(data);
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
  });
}

$(() => poll());
function poll() {
  $.ajax({
    url: "ajax/callWait.php",
    type: "POST",
    cache: false,
  })
  .done(function (data) {
    const dataObj = JSON.parse(data);

    if(dataObj.num){
      customerModalLaunch(dataObj?.id, dataObj.num)
      $.ajax({
        url: "ajax/callHistoryUpdate.php",
        type: "POST",
        cache: false,
      })
      .done(function (data) {
        $('#callHistoryTableWrapper').html(data);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
      });
    }
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
  });
}

setInterval(function(){poll();},1000);