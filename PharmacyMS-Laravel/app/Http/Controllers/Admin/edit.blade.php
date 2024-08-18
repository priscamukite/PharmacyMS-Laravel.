@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ $title }}</h1>
        <table id="expired-products-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will automatically fill this -->
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#expired-products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('products.expired') }}', // Route for fetching expired products
                columns: [
                    { data: 'product', name: 'product' },
                    { data: 'category', name: 'category' },
                    { data: 'price', name: 'price' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'expiry_date', name: 'expiry_date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
