# Phát triển hệ thống chat có ngữ cảnh 
Dựa trên opensource Talk: link [Github](https://github.com/nahid/talk)

## Cài đặt và chạy trên máy local
### Requirements
* PHP 5.5.9 >=
* Composer

### Installation
Sau khi cài đặt composer vào thư mục gốc, nơi chứa file `composer.json` mở terminal (cmd trên window) và gõ lệnh: 

```shell
composer install
```

### Configurations
Sau khi các bước trên cài đặt thành công, sẽ đến cài đặt cơ sở dữ liệu:
 Đầu tiên copy `.env.example` vào `.env`. Nếu chưa có file `.env` thì tạo mới và copy nội dung của file `.env.example` 

```shell
cp .env.example .env
```

Tạo key chạy câu lệnh:

```
php artisan key:generate
```

Mở file `.env` và viết thông tin về cơ sở dữ liệu. Sau đó chạy lệnh migrate từ terminal

```shell
php artisan migrate
```
Sau đó chạy lệnh database seed command.

```shell
php artisan db:seed
```

Thats it

#### For Realtime

Nếu muốn hệ thống này chạy realtime thì phải cấu hình cho hệ thống .Vào file `app/talk.php` và enable broadcast. Sau đó set các thông tin về Pusher. Nếu chưa có tài khoản Pusher thì có thể vào [đây](https://pusher.com/)

```php
return [
    'user' => [
        'model' => 'App\User'
    ],
    'broadcast' => [
        'enable' => false,
        'app_name' => 'talk-example',
        'pusher' => [
            'app_id'        => env('PUSHER_APP_ID'),
            'app_key'       => env('PUSHER_KEY'),
            'app_secret'    => env('PUSHER_SECRET')
        ]
    ]
];
````

> Để có được hiệu suất chat tốt hơn hãy thiết lập **Redis**cho app

Trước khi bắt đầu chat có thể lắng nghe cổng 

```
php artisan queue:listen
```

### Run 

Chạy lệnh trên terminal:

```shell
php artisan serve
```
Bây giờ mở ứng dụng trên browser và vào http://localhost:8000. Sau đó login với tài khoản
> email: talk@example.com    
> password: 123456

![Talk-Example Screenshot](https://snag.gy/CUkAi7.jpg "Talk-Example Project")

## Thêm chức năng tạo ngữ cảnh
- Sử dụng công cụ `CrazyTalkAnimater` để tạo ngữ cảnh.
- Thêm trường background vào bảng `conversations`.
- Viết chức năng update `conversations` để update background.
- Chỉnh sửa function `chatHistory($id)` trong `class MessageController`.
- Chèn text vào ngữ cảnh.

#### 1. Giao diện

> Hiển thị list ngữ cảnh sidebar bên phải. Code giao diện được viết trong file: `views/context/images.blade.php`, css cho ngữ cảnh trong file: `public/chat/css/context.css`
#### 2. Ý tưởng chính update background
 ---  Khi click chọn một ngữ cảnh, thì ảnh đó sẽ được chọn làm background cho cửa sổ chat. Đồng thời thực hiện update trường background trong conversation(mặc định ban đầu background = null). 
--- Image được lưu trong folder: `public/chat/images`, trong database lưu path của ảnh đó.
Chức năng update conversation và hiển thị ngữ cảnh như sau:

Route: `routes/web.php`

```php
Route::group(['prefix'=>'ajax', 'as'=>'ajax::'], function() {
    Route::post('conversation', 'MessageController@updateConversation')->name('conversation.update');
});
```

Sử dụng ajax để lấy conversation_id và image từ form truyền cho Controler để xử lý: `public/chat/js/talj.js`

```javascript
$("a img").on('click', function (e) {
        var img = $(this).attr('src');
        $(".chat-history").hide().css('background', 'url(' + img + ')');

        var $img = $("<img>").attr('src', img).one('load', function () {
          $(".chat-history").fadeIn();
        });
          
        if ($img.get(0).complete) {
          $(".chat-history").fadeIn(2000);
        }

        nameImg = img.split("/")[img.split("/").length-1];
        
        /* xét giá trị truyền vào ajax: id, name_background, url, method*/
        e.preventDefault();
        var url, request, data;
        url = __baseUrl + '/ajax/conversation';
        data = $("#cv_id").val();
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
    });
```

Controler: `Controllers/MessageController.php` : thực hiện update conversation 

```php
public function updateConversation(Request $request)
{
    if ($request->ajax()) {
        $bg = $request->bg;
        $cvId = $request->data;
        $val = $bg . ' ' . $cvId;
        if ($conversation = Talk::updateConversation($cvId, $bg)) {
            $response = array(
                    'status'=>$conversation->background,
                    'msg'=>'successfully',
            );
            return response()->json(['status'=>'successfully '.$conversation->background]);
        }
    }
    return response()->json(['error' => 'error']);
}
```

`Talk::updateConversation($cvId, $bg)` thực hiện update conversaion:

```php
public function updateConversation($cvId, $background)
{
    $conversation = $this->conversation->update($cvId, ['background' => $background]);
    if ($conversation) {
        return $conversation;
    } else {
        return false;
    }
}
```

Lấy object conversation để trả về view hiển thị ngữ cảnh :Edit chaHistory() trong  `MessageController` 

```php
public function chatHistory($id)
{
    $conversations = Talk::getMessagesByUserId($id);
    $user = '';
    $messages = [];
    $conversation = [];
    $cvId = 0;
    if(!$conversations) {
        $user = User::find($id);
    } else {
        $user = $conversations->withUser;
        $messages = $conversations->messages;
        /* lấy conversation_id từ messages */
        foreach ($messages as $message) {
            $cvId = $message['conversation_id'];
            $conversation = Talk::getConversationById($cvId);
        }
    }
    return view('messages.conversations', compact('messages', 'user', 'conversations', 'conversation'));
}
```

`Talk::getConversationById($id)` lấy conversaiton có `id = $id` trong: `vendor/nahid/talk/src/Talk.php`

```php
public function getConversationById($cvId)
{
    $conversation = $this->conversation->getById($cvId);
    if ($conversation) {
        return $conversation;
    }
    return false;
}
```

Trong `vendor/nahid/talk/src/Conversations/ConversationRepository.php`

```php
public function getById($id)
{
    $conversation = $this->find($id);
    if($conversation) {
        return $conversation;
    }
    return false;
}

```

## Template Credit

Template - [Live chat window widget](http://www.bypeople.com/live-chat-window-widget/ ) by [SergioGalindo](http://www.bypeople.com/author/uakala/)


