@extends('backend.app')
@section('title', 'Mission Items - Aboutus Page')

@push('styles')
    <style>
        [type=checkbox]:checked,
        [type=radio]:checked {
            background-size: 40% !important;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Mission Items</h3>

            @can('cms.aboutus_page.our_mission_items.create')
                <a href="{{ route('cms.aboutus_page.our_mission_items.create') }}" class="btn btn-primary py-2 px-4 fw-medium fs-16">
                    <i class="ri-add-line"></i> Add Item
                </a>
            @endcan
        </div>

        <div class="card bg-white border-0 rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="mission-items-table">
                        <thead>
                            <tr>
                                <th>Count Number</th>
                                <th>Sub Title</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var table = $('#mission-items-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('cms.aboutus_page.our_mission_items.index') }}",
                columns: [
                    {
                        data: 'count_number',
                        name: 'count_number'
                    },
                    {
                        data: 'sub_title',
                        name: 'sub_title'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        function changeStatus(event, id) {
            event.preventDefault();
            let isChecked = event.target.checked;

            $.ajax({
                type: 'POST',
                url: "{{ route('cms.aboutus_page.our_mission_items.status', '') }}" + "/" + id,
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                    }
                },
                error: function(error) {
                    console.log(error);
                    toastr.error('Something went wrong');
                }
            });
        }

        function deleteRecord(event, id) {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('cms.aboutus_page.our_mission_items.destroy', '') }}" + "/" + id,
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data.success) {
                            $('#mission-items-table').DataTable().ajax.reload();
                            toastr.success(data.message);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                        toastr.error('Something went wrong');
                    }
                });
            }
        }
    </script>
@endpush
