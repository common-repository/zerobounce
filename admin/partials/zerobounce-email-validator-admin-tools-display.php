<?php

/**
 * Provide a admin area view for the plugin
 *
 * @package    Zerobounce_Email_Validator
 * @subpackage Zerobounce_Email_Validator/admin/partials
 * @author     ZeroBounce (https://zerobounce.net/)
 */
?>

<div class="wrap">
    <header class="p-3 mb-3 bg-white border-bottom">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 mx-3 text-dark text-decoration-none">
                    <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'img/logo.svg'; ?>" class="img-fluid"
                         title="ZeroBounce" alt="ZeroBounce">
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0 fw-bold">
                    <li><a href="<?php echo get_admin_url() . "admin.php?page=zerobounce-email-validator"; ?>"
                           class="nav-link px-2 <?php if (isset($_GET['page']) && $_GET['page'] === "zerobounce-email-validator") {
                               echo "link-secondary";
                           } else {
                               echo "link-dark";
                           } ?>"><?php _e('Dashboard', 'zerobounce-email-validator') ?></a></li>
                    <li><a href="<?php echo get_admin_url() . "admin.php?page=zerobounce-email-validator-settings"; ?>"
                           class="nav-link px-2 <?php if (isset($_GET['page']) && $_GET['page'] === "zerobounce-email-validator-settings") {
                               echo "link-secondary";
                           } else {
                               echo "link-dark";
                           } ?>"><?php _e('Settings', 'zerobounce-email-validator') ?></a></li>
                    <li><a href="<?php echo get_admin_url() . "admin.php?page=zerobounce-email-validator-tools"; ?>"
                           class="nav-link px-2 <?php if (isset($_GET['page']) && $_GET['page'] === "zerobounce-email-validator-tools") {
                               echo "link-secondary";
                           } else {
                               echo "link-dark";
                           } ?>"><?php _e('Tools', 'zerobounce-email-validator') ?></a></li>
                    <li><a href="<?php echo get_admin_url() . "admin.php?page=zerobounce-email-validator-logs"; ?>"
                           class="nav-link px-2 <?php if (isset($_GET['page']) && $_GET['page'] === "zerobounce-email-validator-logs") {
                               echo "link-secondary";
                           } else {
                               echo "link-dark";
                           } ?>"><?php _e('Logs', 'zerobounce-email-validator') ?></a></li>
                </ul>
                <div class="text-end">
                    <span id="zb-current-credits"><div id="zb-current-credits-loader"
                                                       class="spinner-border spinner-border-sm" role="status"><span
                                    class="visually-hidden">0</span></div></span>&nbsp;<span><?php _e('credits', 'zerobounce-email-validator') ?></span>
                    <span class="dashicons dashicons-info-outline"
                          style="vertical-align: middle !important; font-size: 1rem !important;"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="<?php _e('Credits can be used for either validation or scoring. 1 credit = 1 successfully processed email address.', 'zerobounce-email-validator') ?>"></span>
                    <a href="https://www.zerobounce.net/members/pricing" class="btn btn-warning border-0 fw-bold"
                       target="_blank"
                       title="<?php _e('Buy Credits', 'zerobounce-email-validator') ?>"><?php _e('Buy Credits', 'zerobounce-email-validator') ?></a>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid g-0">
        <div class="row">
            <div class="row mx-0">
                <div class="card rounded-0 p-3 m-0">
                    <div class="card-body">
                        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST"
                              id="validate-form" disabled>
                            <div class="validate-form-container col-6">
                                <p><?php _e('Enter an email below to validate', 'zerobounce-email-validator') ?></p>
                                <div class="form-input form-group field-input-email">
                                    <input type="text" class="email-form-input" name="email" id="email"
                                           placeholder="<?php _e('Email', 'zerobounce-email-validator') ?>" value=""
                                           required>
                                    <input type="submit" name="submit" id="submit" class="button button-primary"
                                           style="min-height: 33px;"
                                           value="<?php _e('Validate', 'zerobounce-email-validator') ?>">
                                </div>

                                <div id="validate-form-result" style="overflow-x:auto;">
                                    <p class="text-danger"></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mx-0 mt-3 zerobounce-bulk-validator">
                <div class="card rounded-0 p-3 m-0">
                    <div class="card-body row m-0">
                        <div class="col-12 col-xl-6 pl-0">
                            <p><?php _e('Bulk email validator', 'zerobounce-email-validator') ?></p>
                            <div class="form-input form-group field-input-email">
                                <form id="bulk-validation-form" class="d-flex flex-column">
                                    <div class="form-group zerobounce-upload-section p-3 mb-3" id="fileUploadDiv">
                                        <label for="csvUpload">Upload CSV File</label>
                                        <div id="fileNameDisplay" class="align-items-center justify-content-center mt-2"
                                             style="display: none;">
                                            <span id="fileName"></span>
                                            <button type="button" class="btn btn-sm remove-button" id="removeFileBtn">
                                                &times;
                                            </button>
                                        </div>
                                        <input type="file" class="form-control-file d-none" id="csvUpload"
                                               name="csvUpload" accept=".csv">
                                    </div>
                                    <input type="submit" class="button button-primary mb-3"
                                           value="<?php _e('Validate Bulk', 'zerobounce-email-validator') ?>"/>
                                    <div id="zb-bulk-loader" class="zb-loader-overlay">
                                        <div class="zb-loader"></div>
                                    </div>
                                </form>
                                <form id="bulk-validation-form-manual" class="d-flex flex-column">
                                    <div class="flex-column" id="manualUpload" style="display: flex;">
                                        <p class="text-center h5">OR</p>
                                        <label for="manual-upload" class="mb-2">Enter emails manually (up to 50)</label>
                                        <textarea id="manual-upload" name="manual-upload" rows="5" placeholder="email1@example.com, email2@example.com, ..."
                                                  aria-label=""></textarea>
                                    </div>
                                    <input type="submit" class="button button-primary mt-2"
                                           value="<?php _e('Validate Bulk', 'zerobounce-email-validator') ?>"/>
                                    <div id="zb-bulk-loader-manual" class="zb-loader-overlay">
                                        <div class="zb-loader"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-12 mt-3 mt-xl-0 col-xl-6">
                            <div class="d-flex flex-column position-relative" id="csv-results" style="display: none;">
                                <p class="mb-3"><?php _e('CSV File Validation Status', 'zerobounce-email-validator') ?></p>
                                <table class="table table-bordered" id="csvResultsTable">
                                    <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th class="text-center" style="min-width: 200px;" width="20%">Date</th>
                                        <th class="text-center" style="min-width: 100px;" width="15%">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No records</td>
                                    </tr>
                                    <!-- Dynamic rows for each uploaded CSV file -->
                                    </tbody>
                                </table>
                                <nav aria-label="Files pagination">
                                    <ul class="pagination small-pagination justify-content-center mb-5" id="pagination">
                                        <!-- Pagination links will be dynamically inserted here -->
                                    </ul>
                                </nav>
                                <div id="zb-results-loader" class="zb-loader-overlay">
                                    <div class="zb-loader mt-5"></div>
                                </div>
                            </div>

                            <div class="d-flex flex-column" id="manual-results" style="display: none;">
                                <p class="mb-3"><?php _e('Bulk email validator results', 'zerobounce-email-validator') ?></p>
                                <table class="table table-bordered" id="resultsTable">
                                    <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th class="text-center" style="min-width: 100px;" width="17.5%">Status</th>
                                        <th class="text-center" style="min-width: 100px;" width="17.5%">Substatus</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Check validation logs <a href="<?php echo admin_url('admin.php?page=zerobounce-email-validator-logs') ?>">here</a>.</td>
                                    </tr>
                                    <!-- Dynamic rows will be appended here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal with Custom CSS -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header error-header"> <!-- Custom error header -->
                    <h6 class="modal-title" id="errorModalLabel">Error</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body error-body"> <!-- Custom error body -->
                    An error occurred during the request. Please try again later.
                </div>
                <div class="modal-footer bulk">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal with Custom CSS -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header success-header"> <!-- Custom success header -->
                    <h6 class="modal-title" id="successModalLabel">Success</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body success-body"> <!-- Custom success body -->
                    The request was successful!
                </div>
                <div class="modal-footer bulk">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>