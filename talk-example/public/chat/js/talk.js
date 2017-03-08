$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('#talkSendMessage').on('submit', function(e) {
        e.preventDefault();
        var url, request, tag, data;
        tag = $(this);
        url = __baseUrl + '/ajax/message/send';
        data = tag.serialize();

        request = $.ajax({
            method: "post",
            url: url,
            data: data,
        });

        request.done(function (response) {
            if (response.status == 'success') {
                $('#talkMessages').append(response.html);
                tag[0].reset();
            }
        });

    });
    
    $("a img").on('click', function (e) {

      var img = $(this).attr('src');
      $(".chat-history").hide().css('background', 'url(' + img + ')');

      var $img = $("<img>").attr('src', img).one('load', function () {
        $(".chat-history").fadeIn();
      });
        
      if ($img.get(0).complete) {
        $(".chat-history").fadeIn(2000);
      }

      // Get file name from src
      /*String.prototype.filename=function(extension){
          var s= this.replace(/\\/g, '/');
          s= s.substring(s.lastIndexOf('/')+ 1);
          return extension? s.replace(/[?#].+$/, ''): s.split('.')[0];
      }
      nameImg = img.filename() + '.gif'; // Name image
  */    
      nameImg = img.split("/")[img.split("/").length-1];
      
      /* xét giá trị truyền vào ajax: id, name_background, url, method*/
      e.preventDefault();
      var url, request, data;
      
      url = __baseUrl + '/ajax/conversation';
      data = $("#cv_id").val();
      /*tag = $("#form-bg");
      data = tag.serialize();*/
      
      console.log(data + " " + nameImg + " " + url + " " + img);
      
      /* xử lý ajax submit form*/
      request = $.ajax({
        url: url,
        method: "post",
        beforeSend: function (xhr) {
          var token = $('meta[name="csrf_token"]').attr('content');

          if (token) {
                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
          }
        },
        /*dataType: 'JSON',*/
        data: {data: data, bg: img},
        /*data: { testdata : 'testdatacontent' },*/
      });
      
      request.done(function (response) {
        alert(response.status + " " + response.msg);
        if (response.status == 'success') {
          alert();
        }
      });
      
      request.fail(function (response) {
        console.log(response);
      });
      /*$.ajax({
        type: "GET",
        url: "...",
        data: "{imgName : nameImg}";
        success: function(data) {

        }
      });*/

    });


    $('body').on('click', '.talkDeleteMessage', function (e) {
        e.preventDefault();
        var tag, url, id, request;

        tag = $(this);
        id = tag.data('message-id');
        url = __baseUrl + '/ajax/message/delete/' + id;

        if(!confirm('Do you want to delete this message?')) {
            return false;
        }

        request = $.ajax({
            method: "post",
            url: url,
            data: {"_method": "DELETE"}
        });

        request.done(function(response) {
           if (response.status == 'success') {
                $('#message-' + id).hide(500, function () {
                    $(this).remove();
                });
           }
        });
    })
    
    
});
