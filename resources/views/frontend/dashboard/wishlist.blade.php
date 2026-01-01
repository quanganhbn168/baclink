@extends('frontend.dashboard.layout')

@section('title', 'Sản phẩm yêu thích')

@section('dashboard_content')
    <h3 class="mb-4">Sản phẩm yêu thích</h3>
    <div class="row">
        @forelse($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <a href="{{ route('frontend.product.show', $product->slug) }}">
                        <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="{{ route('frontend.product.show', $product->slug) }}" class="text-dark text-decoration-none">{{ $product->name }}</a></h5>
                        <p class="card-text text-danger fw-bold">{{ number_format($product->price) }}đ</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <button class="btn btn-sm btn-outline-danger w-100 btn-remove-wishlist" data-id="{{ $product->id }}">
                            <i class="fa fa-trash"></i> Xóa khỏi yêu thích
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">Danh sách yêu thích của bạn đang trống.</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-remove-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const url = `/wishlist/remove/${productId}`;

            if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
                return;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    this.closest('.col-md-4').remove();
                    // Có thể thêm thông báo thành công ở đây
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endpush