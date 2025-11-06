
<?php
include 'partials/sidebar.php';
include 'partials/topbar.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sales Reports</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Generate Sales Report</h6>
                    <button id="generateReportBtn" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Generate Report
                    </button>
                </div>
                <div class="card-body">
                    <div id="reportContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'partials/footer.php';
?>

<!-- Custom scripts for this page -->
<script src="../js/admin-reports.js"></script>
