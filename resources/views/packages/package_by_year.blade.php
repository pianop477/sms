@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 20px;
        }

        .card-body {
            padding: 5px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
            font-size: 14px;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .list-group-item-custom {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }

        .list-group-item-custom:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .list-group-item-custom h6 {
            margin: 0;
            font-weight: 600;
        }

        .year-link {
            text-decoration: none;
            color: inherit;
        }

        .year-link:hover {
            text-decoration: none;
            color: inherit;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
            text-align: center;
            font-size: 14px;
        }

        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #e9ecef;
            text-align: center;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .badge-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
        }

        .badge-danger-custom {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
        }

        .badge-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
        }

        .action-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .action-list li {
            display: inline-block;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 16px;
            transition: all 0.3s;
            padding: 5px;
            border-radius: 5px;
        }

        .action-btn:hover {
            transform: scale(1.2);
            color: var(--secondary);
        }

        .action-btn-danger:hover {
            color: var(--danger);
        }

        .action-btn-info:hover {
            color: var(--info);
        }

        .floating-icons {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 40px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .modal-title {
            font-weight: 600;
        }

        .close {
            color: white;
            opacity: 0.8;
        }

        .close:hover {
            color: white;
            opacity: 1;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 18px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .action-list {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    <div class="">
        <div class="row">
            <!-- Year List Panel -->
            <div class="col-lg-4 mb-4">
                <div class="glass-card">
                    <div class="card-header-custom">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h4 class="header-title text-white">
                                    <i class="fas fa-calendar-alt me-2"></i> Packages by Year
                                </h4>
                            </div>
                            <div class="col-2 text-end">
                                <a href="{{route('home')}}" class="btn btn-back">
                                    <i class="fas fa-arrow-circle-left"></i>
                                </a>
                            </div>
                        </div>
                        <i class="fas fa-gift floating-icons"></i>
                    </div>
                    <div class="card-body">
                        <p class="text-danger mb-3"><i class="fas fa-info-circle me-2"></i> Select Year</p>

                        @if ($groupedByYear->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No holiday packages available!</p>
                            </div>
                        @else
                            <div class="list-group">
                                @foreach ($groupedByYear as $year => $package)
                                    <a href="{{route('package.byClass', ['year' => $year])}}" class="year-link">
                                        <div class="list-group-item-custom">
                                            <h6 class="text-primary">
                                                <i class="fas fa-chevron-right me-2"></i> {{ $year ?? now()->year }}
                                            </h6>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Packages Panel -->
            <div class="col-lg-8">
                <div class="glass-card">
                    <div class="card-header-custom">
                        <div class="row align-items-center">
                            <div class="col-9">
                                <h4 class="header-title text-white">
                                    <i class="fas fa-history me-2"></i> Recent Holiday Packages
                                </h4>
                            </div>
                            <div class="col-3 text-end">
                                <button type="button" class="btn btn-primary-custom" data-toggle="modal" data-target=".packageModal">
                                    <i class="fas fa-upload me-1"></i> Upload Package
                                </button>
                            </div>
                        </div>
                        <i class="fas fa-file-alt floating-icons"></i>
                    </div>
                    <div class="card-body">
                        @if ($recentPackages->isEmpty())
                            <div class="table-container">
                                <table class="table table-custom table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Class</th>
                                            <th>Term</th>
                                            <th>Issued by</th>
                                            <th>Issued at</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fas fa-calendar-times"></i>
                                                    <p>No recent holiday packages available!</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="table-container">
                                <div class="table-responsive">
                                    <table class="table table-custom table-responsive-md" id="">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Class</th>
                                                <th>Issued by</th>
                                                <th>Status</th>
                                                <th>Issued at</th>
                                                <th>Downloads</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentPackages as $recent)
                                                <tr>
                                                    <td class="text-capitalize">{{$recent->title}}</td>
                                                    <td class="text-uppercase">{{$recent->class_code}}</td>
                                                    <td class="text-capitalize">{{$recent->first_name}}. {{$recent->last_name[0]}}.</td>
                                                    <td>
                                                        @if ($recent->is_active == true)
                                                            <span class="badge bg-success text-white">Active <i class="fas fa-unlock"></i></span>
                                                        @else
                                                            <span class="badge bg-danger text-white">Locked <i class="fas fa-lock"></i></span>
                                                        @endif
                                                    </td>
                                                    <td>{{\Carbon\Carbon::parse($recent->created_at)->format('d-m-Y') ?? \Carbon\Carbon::parse($recent->updated_at)->format('d-m-Y H:i')}}</td>
                                                    <td>
                                                        <span class="badge-primary-custom">{{$recent->download_count}}</span>
                                                    </td>
                                                    <td>
                                                        <ul class="action-list">
                                                            @if ($recent->is_active == true)
                                                                <li>
                                                                    <form action="{{route('deactivate.holiday.package', ['id' => Hashids::encode($recent->id)])}}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit" class="action-btn" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this package?')">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <form action="{{route('activate.holiday.package', ['id' => Hashids::encode($recent->id)])}}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit" class="action-btn" title="Activate" onclick="return confirm('Are you sure you want to activate this package?')">
                                                                            <i class="fas fa-eye-slash"></i>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <a href="{{route('download.holiday.package', ['id' => Hashids::encode($recent->id), 'preview' => true])}}"
                                                                   class="action-btn action-btn-info"
                                                                   title="Download"
                                                                   target="_blank"
                                                                   onclick="return confirm('Are you sure you want to download this package?')">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('delete.holiday.package', ['id' => Hashids::encode($recent->id)])}}"
                                                                   class="action-btn action-btn-danger"
                                                                   title="Delete"
                                                                   onclick="return confirm('Are you sure you want to delete this package?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Package Modal -->
    <div class="modal fade packageModal" tabindex="-1" role="dialog" aria-labelledby="packageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-upload me-2"></i> Upload New Holiday Package
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('package.upload')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">
                                    Package Title <span class="required-star">*</span>
                                </label>
                                <input type="text" name="title" class="form-control" id="title"
                                       placeholder="Package name" value="{{old('title')}}" required>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="class" class="form-label">
                                    Class <span class="required-star">*</span>
                                </label>
                                <select name="class" id="class" class="form-control text-uppercase" required>
                                    <option value="">-- Select Class --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{$class->id}}" {{old('class') == $class->id ? 'selected' : ''}}>
                                            {{$class->class_name}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="term" class="form-label">
                                    Term <span class="required-star">*</span>
                                </label>
                                <select name="term" id="term" class="form-control form-control-custom" required>
                                    <option value="">-- Select Term --</option>
                                    <option value="i" {{old('term') == 'i' ? 'selected' : ''}}>Term 1</option>
                                    <option value="ii" {{old('term') == 'ii' ? 'selected' : ''}}>Term 2</option>
                                </select>
                                @error('term')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="package_file" class="form-label">
                                    Upload File <span class="required-star">*</span>
                                </label>
                                <input type="file" name="package_file" class="form-control form-control-custom" id="package_file" required>
                                @error('package_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="form-control form-control-custom" id="description"
                                          rows="3" placeholder="Package description">{{old('description')}}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary-custom">Upload Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    var forms = document.getElementsByClassName('needs-validation');
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener('submit', function(event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();
        });
    </script>
@endsection
