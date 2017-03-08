@extends('layouts.chat')

@section('content')
@foreach($messages as $message)
    <div class="chat-history"
    	style="background-image: url({{$message->conversation->background}});">
        <ul id="talkMessages">
                @if($message->sender->id == auth()->user()->id)
                    <!-- {{var_dump($message->conversation->background)}} -->
                    <li class="clearfix" id="message-{{$message->id}}">
                        <div class="message-data align-right">
                            <span class="message-data-time" >{{$message->humans_time}} ago</span> &nbsp; &nbsp;
                            <span class="message-data-name" >{{$message->sender->name}}</span>
                            <a href="#" class="talkDeleteMessage" data-message-id="{{$message->id}}" title="Delete Message"><i class="fa fa-close"></i></a>
                        </div>
                        <div class="message other-message float-right">
                            {{$message->message}}
                        </div>
                    </li>
                @else

                    <li id="message-{{$message->id}}">
                        <div class="message-data">
                            <span class="message-data-name"> <a href="#" class="talkDeleteMessage" data-message-id="{{$message->id}}" title="Delete Messag"><i class="fa fa-close" style="margin-right: 3px;"></i></a>{{$message->sender->name}}</span>
                            <span class="message-data-time">{{$message->humans_time}} ago</span>
                        </div>
                        <div class="message my-message">
                            {{$message->message}}
                        </div>
                    </li>
                @endif


        </ul>

    </div> <!-- end chat-history -->
@endforeach

@endsection
