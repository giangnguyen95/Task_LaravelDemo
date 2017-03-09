<div class="context">
	<div class="title">
		<h4>
			<i class="fa fa-picture-o" aria-hidden="true"></i>
			Chọn ngữ cảnh
		</h4>
	</div>
	<form id="form-bg">
		<!-- <input type="hidden" name="_id" value="{{@request()->route('id')}}"> -->
		<input type="hidden" name="_cv" id ="cv_id" value="{{$conversations->id}}">
		<ul class="list_img">
			<li>
				<a class="background_img" href="#">
					<img src="{{asset('chat/images/bg_1.gif')}}" width="100px" height="100px">
				</a>
			</li>
			<li>
				<a class="background_img" href="#">
					<img src="{{asset('chat/images/bg_2.gif')}}" width="100px" height="100px">
				</a>
			</li>
			<li>
				<a class="background_img" href="#">
					<img src="{{asset('chat/images/bg_3.gif')}}" width="100px" height="100px">
				</a>
			</li>
			<li>
				<a class="background_img" href="#">
					<img src="{{asset('chat/images/bg_4.gif')}}" width="100px" height="100px">
				</a>
			</li>
		</ul>
	</form>
</div>

