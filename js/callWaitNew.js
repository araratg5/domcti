var waitElem = waitElem || {};
var params = new URL(document.location).searchParams;

waitElem.fire = {
  init: function () {
    this.setParameters();
    this.bindEvent();
  },

  setParameters: function () {
    this.shopId = $('#shopid').val();
    this.firstExecuted = 0;
    this.waitElemDataStore = new Firebase(
      "https://araratcti-default-rtdb.firebaseio.com/"
    );
  },

  bindEvent: function () {
    var self = this;
    this.waitElemDataStore.child(this.shopId).on("child_added", function (data) {
      if(self.firstExecuted == 1){
        var json = data.val();
        $.ajax({
          url: "ajax/callReceiver.php",
          type: "POST",
          data: {
              shop_id: self.shopId,
              num: json.tel
            },
          cache: false,
        })
        .done(function (data) {
          const dataObj = JSON.parse(data);
          let cid = '';
          let name = '';
          let address = '';
          customerModalLaunch(dataObj?.id, dataObj.num, 1)
          if(dataObj?.cid!=null){
            cid = dataObj?.cid;
          }
          if(dataObj?.name!=null){
            name = dataObj?.name;
          }
          if(dataObj?.address!=null){
            address = dataObj?.address;
          }
          $('#latestList tbody,#historyList tbody').prepend(`<tr data-customer-id="${dataObj?.id}" data-customer-num="${json.tel}" ${dataObj?.rating}>
  <td><input type="checkbox" value="${dataObj?.id}" ></td>
  <td style="width:50px !important;">1</td>
  <td style="width:165px !important; text-align:center;">${json.time}</td>
  <td style="width:90px !important; text-align:center;">${cid}</td>
  <td style="width:300px !important; text-align:left;">${name}</td>
  <td style="width:130px !important; text-align:center;">${dataObj?.separated_num}</td>
  <td>${address}</td>
</tr>`);  
          $('#latestList tbody tr:last-child,#historyList tbody tr:last-child').remove();
          $('#latestList tbody tr td:nth-child(2),#historyList tbody tr td:nth-child(2)').each(function(i, elem) {
            $(elem).text(i + 1);
          })
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
        });
      }
      self.firstExecuted = 1;
    });
  },
};

$(function () {
  waitElem.fire.init();
});
