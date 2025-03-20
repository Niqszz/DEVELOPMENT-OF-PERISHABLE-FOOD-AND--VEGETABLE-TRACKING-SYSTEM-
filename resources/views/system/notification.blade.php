@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row p-5">
        <div class="product-management-content" style="overflow-y: scroll">
            <div class="col-md-12">
                <!-- Search and Buttons -->
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <h1>Notification</h1>
                </div>
    
                <!-- Table -->
                <table class="table table-hover table-bordered rounded">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Product Name</th>
                            <th>Device</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Problem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample Rows -->
                        <tr>
                            <td><img src="/path/to/carrot.png" alt="Carrot" class="img-thumbnail" style="width: 30px;"></td>
                            <td>Carrot</td>
                            <td>Device 1</td>
                            <td>Vegetables</td>
                            <td><span class="badge bg-success">Good</span></td>
                            <th>Spoiledge warning : Unstable Environment</th>
                        </tr>
                        <tr>
                            <td><img src="/path/to/carrot.png" alt="Carrot" class="img-thumbnail" style="width: 30px;"></td>
                            <td>Carrot</td>
                            <td>Device 2</td>
                            <td>Vegetables</td>
                            <td><span class="badge bg-warning">Average</span></td>
                            <th>Spoiledge warning : Unstable Environment</th>
                        </tr>
                        <tr>
                            <td><img src="/path/to/apple.png" alt="Apple" class="img-thumbnail" style="width: 30px;"></td>
                            <td>Apple</td>
                            <td>Device 1</td>
                            <td>Fruits</td>
                            <td><span class="badge bg-danger">Bad</span></td>
                            <th>Spoiledge warning : Unstable Environment</th>
                        </tr>
                        <!-- Repeat similar rows as needed -->
                    </tbody>
                </table>
    
                <!-- Pagination -->
                <nav aria-label="Page navigation example" class="d-flex justify-content-center mt-3">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#">&#60;</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item active"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">&#62;</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

@endsection