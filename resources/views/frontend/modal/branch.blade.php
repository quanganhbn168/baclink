<!-- Modal -->
<div class="modal fade" id="modaladdress" tabindex="-1" role="dialog" aria-labelledby="modaladdressLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modaladdressLabel">Danh sách chi nhánh</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				@php
				$branches = DB::table('branches')->where("status",1)->get();
				@endphp
				<div class="branches">
					@foreach($branches as $branch)
					<div class="branch-item">
						<p class="branch-name text-primary font-weight-bold text-uppercase">{{$branch->name}}</p>
						<p class="branch-name">
							<strong>Địa chỉ:</strong>{{$branch->address}}
						</p>
						<p class="d-flex">
							<span><strong>Điện thoại</strong></span>
							<ul class="phone">
								@foreach(explode("-",$branch->phone) as $phone)
								<li><a href="tel:{{$phone}}">{{$phone}}</a></li>
								@endforeach
							</ul>
						</p>
						<p><strong>Email:</strong><a href="mailto:{{$branch->email}}">{{$branch->email}}</a></p>
					</div>
					@endforeach
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
			</div>
		</div>
	</div>
</div>