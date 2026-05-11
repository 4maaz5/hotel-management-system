@extends('layout.master')
@section('title', 'Dashboard | Employee')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Database Backup</h4>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Backup ID</th>
                                                <th>Backup Type</th>
                                                <th>Created On</th>
                                                <th>Size</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>BKP-001</td>
                                                <td>Database</td>
                                                <td>2025-01-26</td>
                                                <td>5MB</td>
                                                <td><span class="badge badge-success">Success</span></td>

                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>BKP-002</td>
                                                <td>DB + Files</td>
                                                <td>2025-01-20</td>
                                                <td>250MB</td>
                                                <td><span class="badge badge-success">Success</span></td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @endsection
