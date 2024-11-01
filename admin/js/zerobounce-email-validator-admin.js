(function ($) {
    'use strict';

    $(function () {

        $('#validate-form').prop('disabled', false);

        let tooltipelements = document.querySelectorAll("[data-bs-toggle='tooltip']");
        tooltipelements.forEach((el) => {
            new bootstrap.Tooltip(el);
        });

        if ($("#zb-current-credits").length > 0) {
            $.ajax({
                data: {
                    'action': 'zerobounce_current_credits',
                    'nonce': params.ajax_current_credits_nonce
                },
                dataType: 'json',
                url: params.ajax_url,
                type: 'POST',
            })
                .done(function (response) {
                    $("#zb-current-credits").text(response.data);
                })
                .fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseJSON.data.reason);
                })
                .always(function () {
                    $("#zb-current-credits-loader").remove();
                });
        }


        $('#bulk-validation-form').submit(function (event) {
        });


        $('#validate-form').submit(function (event) {
            event.preventDefault();

            var ajax_form_data = $("#validate-form").serializeArray();

            $('#submit').prop('disabled', true);
            $('#submit').val("Please wait...");

            $.ajax({
                data: {
                    'action': 'zerobounce_validate_email_test',
                    'email': ajax_form_data[0].value,
                    'nonce': params.ajax_validation_nonce
                },
                dataType: 'json',
                url: params.ajax_url,
                type: 'POST',
            })
                .done(function (response) {
                    $('#validate-form-result .text-danger').text('');
                    if ($("#verifyEmailResult").length === 0) {
                        $('#validate-form-result').append(`<table id="verifyEmailResult">
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td>Status</td>
                                <td id="verifyEmailStatus">${response.data.status}</td>
                            </tr>
                            <tr>
                                <td>Sub-Status</td>
                                <td id="verifyEmailSubStatus">${response.data.sub_status}</td>
                            </tr>
                            <tr>
                                <td>Free Email</td>
                                <td id="verifyEmailFreeEmail">${response.data.free_email}</td>
                            </tr>
                            <tr>
                                <td>Did you mean?</td>
                                <td id="verifyEmailDyM">${response.data.did_you_mean}</td>
                            </tr>
                            <tr>
                                <td>Account</td>
                                <td id="verifyEmailAccount">${response.data.account}</td>
                            </tr>
                            <tr>
                                <td>Domain</td>
                                <td id="verifyEmailDomain">${response.data.domain}</td>
                            </tr>
                            <tr>
                                <td>Domain Age</td>
                                <td id="verifyEmailDomainAge">${response.data.domain_age_days} (days)</td>
                            </tr>
                            <tr>
                                <td>SMTP Provider</td>
                                <td id="verifyEmailSmtpProvider">${response.data.smtp_provider}</td>
                            </tr>
                            <tr>
                                <td>MX Found</td>
                                <td id="verifyEmailMxFound">${response.data.mx_found}</td>
                            </tr>
                            <tr>
                                <td>MX Record</td>
                                <td id="verifyEmailMxRecord">${response.data.mx_record}</td>
                            </tr>
                            <tr>
                                <td>Firstname</td>
                                <td id="verifyEmailFirstname">${response.data.firstname}</td>
                            </tr>
                            <tr>
                                <td>Lastname</td>
                                <td id="verifyEmailLastname">${response.data.lastname}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td id="verifyEmailGender">${response.data.gender}</td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td id="verifyEmailCountry">${response.data.country}</td>
                            </tr>
                            <tr>
                                <td>Region</td>
                                <td id="verifyEmailRegion">${response.data.region}</td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td id="verifyEmailCity">${response.data.city}</td>
                            </tr>
                            <tr>
                                <td>ZIP Code</td>
                                <td id="verifyEmailZipCode">${response.data.zipcode}</td>
                            </tr>
                            <tr>
                                <td>Processed At</td>
                                <td id="verifyEmailProcessedAt">${response.data.processed_at}</td>
                            </tr>
                        </tbody>
                    </table>`);
                    } else {
                        $("#verifyEmailStatus").text(response.data.status);
                        $("#verifyEmailSubStatus").text(response.data.sub_status);
                        $("#verifyEmailFreeEmail").text(response.data.free_email);
                        $("#verifyEmailDyM").text(response.data.did_you_mean);
                        $("#verifyEmailAccount").text(response.data.account);
                        $("#verifyEmailDomain").text(response.data.domain);
                        $("#verifyEmailDomainAge").text(response.data.domain_age_days);
                        $("#verifyEmailSmtpProvider").text(response.data.smtp_provider);
                        $("#verifyEmailMxFound").text(response.data.mx_found);
                        $("#verifyEmailMxRecord").text(response.data.mx_record);
                        $("#verifyEmailFirstname").text(response.data.firstname);
                        $("#verifyEmailLastname").text(response.data.lastname);
                        $("#verifyEmailGender").text(response.data.gender);
                        $("#verifyEmailCountry").text(response.data.country);
                        $("#verifyEmailRegion").text(response.data.region);
                        $("#verifyEmailCity").text(response.data.city);
                        $("#verifyEmailZipCode").text(response.data.zipcode);
                        $("#verifyEmailProcessedAt").text(response.data.processed_at);
                    }
                })
                .fail(function (jqXHR, textStatus) {
                    $('#validate-form-result .text-danger').text(jqXHR.responseJSON.data.reason);
                })
                .always(function () {
                    event.target.reset();
                    $('#submit').val("Validate");
                    $('#submit').prop('disabled', false);
                });
        });

        if ($("#verifyEmailsChart").length > 0) {
            var validationsOptions = {
                colors: ['#3ecf8f', '#e65849', '#ff978a', '#ffbe43', '#dcdcdc', '#014b70', '#1e8bc2', '#030637'],
                series: [
                    {
                        name: 'Valid',
                        data: [],
                    },
                    {
                        name: 'Invalid',
                        data: [],
                    },
                    {
                        name: 'Catch-All',
                        data: [],
                    },
                    {
                        name: 'Unknown',
                        data: [],
                    },
                    {
                        name: 'Spamtrap',
                        data: [],
                    },
                    {
                        name: 'Abuse',
                        data: [],
                    },
                    {
                        name: 'Do Not Mail',
                        data: [],
                    },
                    {
                        name: 'Block Free Services',
                        data: [],
                    }
                ],
                chart: {
                    height: 350,
                    type: 'area',
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'datetime',
                    categories: [],
                    labels: {}
                },
                tooltip: {
                    x: {},
                },

                title: {
                    text: 'Validations this month:',
                    align: 'left',
                    floating: false,
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold',
                        fontFamily: undefined,
                        color: '#263238'
                    },
                },
                subtitle: {
                    text: 'Each type of validation status returned by the ZeroBounce API',
                    align: 'left',
                },
            };

            $.ajax({
                data: {
                    'action': 'zerobounce_validation_logs',
                    'nonce': params.ajax_validation_charts_nonce
                },
                dataType: 'json',
                url: params.ajax_url,
                type: 'POST',
            })
                .done(function (response) {

                    var grand_total = 0;

                    $.each(response.data.count, function (index, value) {

                        validationsOptions.xaxis.categories.push(value.date);

                        validationsOptions.series[0].data.push(value.valid);
                        validationsOptions.series[1].data.push(value.invalid);
                        validationsOptions.series[2].data.push(value.catchall);
                        validationsOptions.series[3].data.push(value.unknown);
                        validationsOptions.series[4].data.push(value.spamtrap);
                        validationsOptions.series[5].data.push(value.abuse);
                        validationsOptions.series[6].data.push(value.do_not_mail);
                        validationsOptions.series[7].data.push(value.no_free_service);

                        if (value.total !== 0)
                            grand_total += value.total;
                    });

                    var validationsChart = new ApexCharts(document.querySelector("#verifyEmailsChart"), validationsOptions);
                    validationsChart.render();

                    validationsChart.updateOptions({
                        title: {
                            text: 'Validations this month: ' + grand_total,
                        }
                    });
                })
                .fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseJSON.data.reason);
                })
                .always(function () {
                });
        }
        // Hide credits section
        // if ($("#creditUsageChart").length > 0) {
        //     var creditUsageOptions = {
        //         series: [{
        //             name: "Credits",
        //             data: []
        //         }],
        //         chart: {
        //             type: 'area',
        //             height: 350,
        //             zoom: {
        //                 enabled: false
        //             }
        //         },
        //         dataLabels: {
        //             enabled: false
        //         },
        //         stroke: {
        //             curve: 'straight'
        //         },
        //
        //         title: {
        //             text: 'Credits used this month: ',
        //             align: 'left',
        //             floating: false,
        //             style: {
        //                 fontSize: '14px',
        //                 fontWeight: 'bold',
        //                 fontFamily: undefined,
        //                 color: '#263238'
        //             },
        //         },
        //         subtitle: {
        //             text: 'A credit is used for each validation done using the ZeroBounce API',
        //             align: 'left'
        //         },
        //         labels: [],
        //         xaxis: {
        //             type: 'datetime',
        //         },
        //         yaxis: {
        //             opposite: true
        //         },
        //         legend: {
        //             horizontalAlign: 'left'
        //         }
        //     };
        //
        //     $.ajax({
        //             data: {
        //                 'action': 'zerobounce_credit_usage_logs',
        //                 'nonce': params.ajax_credit_usage_charts_nonce
        //             },
        //             dataType: 'json',
        //             url: params.ajax_url,
        //             type: 'POST',
        //         })
        //         .done(function(response) {
        //
        //             var grand_total = 0;
        //
        //             $.each(response.data.count, function(index, value) {
        //
        //                 creditUsageOptions.labels.push(value.date);
        //
        //                 creditUsageOptions.series[0].data.push(value.credits_used);
        //
        //                 if (value.credits_used !== 0)
        //                     grand_total += value.credits_used;
        //             });
        //
        //             var creditUsageChart = new ApexCharts(document.querySelector("#creditUsageChart"), creditUsageOptions);
        //             creditUsageChart.render();
        //
        //             creditUsageChart.updateOptions({
        //                 title: {
        //                     text: 'Credits used this month: ' + grand_total,
        //                 }
        //             });
        //         })
        //         .fail(function(jqXHR, textStatus) {
        //             console.log(jqXHR.responseJSON.data.reason);
        //         })
        //         .always(function() {});
        // }

        if ($("#logsTable").length > 0) {
            var table = $('#logsTable').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                ajax: {
                    url: params.ajax_url,
                    data: {
                        'action': 'zerobounce_validation_full_logs',
                        'nonce': params.ajax_validation_full_logs_nonce
                    },
                },
                columnDefs: [{
                    targets: 7,
                    data: null,
                    defaultContent: '<button type="button" class="btn btn-info text-white">View</button>',
                },],
                columns: [
                    {
                        data: 'id'
                    },
                    {
                        data: 'source'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'sub_status'
                    },
                    {
                        data: 'ip_address'
                    },
                    {
                        data: 'date_time'
                    },
                ],
                order: [
                    [6, 'desc']
                ],
                "language": {
                    "processing": "<div class=\"spinner-border\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div>"
                }
            });

            new $.fn.dataTable.FixedHeader(table);

            $('#logsTable tbody').on('click', 'button', function () {
                let tr = $(this).closest('tr');
                let row = table.row(tr.hasClass('parent') ? tr : tr.prev());
                let data = row.data();

                $.ajax({
                    data: {
                        'action': 'zerobounce_validation_single_log',
                        'id': data.id,
                        'nonce': params.ajax_validation_single_log_nonce
                    },
                    dataType: 'json',
                    url: params.ajax_url,
                    type: 'POST',
                })
                    .done(function (response) {

                        if ($("#logInspectResult").length === 0) {
                            $('#log-inspect-result').append(`<table id="logInspectResult" class="table table-sm table-hover">
                                <thead></thead>
                                <tbody>
                                    <tr>
                                        <td>Status</td>
                                        <td id="logInspectStatus">${response.data.status}</td>
                                    </tr>
                                    <tr>
                                        <td>Sub-Status</td>
                                        <td id="logInspectSubStatus">${response.data.sub_status}</td>
                                    </tr>
                                    <tr>
                                        <td>Free Email</td>
                                        <td id="logInspectFreeEmail">${response.data.free_email}</td>
                                    </tr>
                                    <tr>
                                        <td>Did you mean?</td>
                                        <td id="logInspectDyM">${response.data.did_you_mean}</td>
                                    </tr>
                                    <tr>
                                        <td>Account</td>
                                        <td id="logInspectAccount">${response.data.account}</td>
                                    </tr>
                                    <tr>
                                        <td>Domain</td>
                                        <td id="logInspectDomain">${response.data.domain}</td>
                                    </tr>
                                    <tr>
                                        <td>Domain Age</td>
                                        <td id="logInspectDomainAge">${response.data.domain_age_days} (days)</td>
                                    </tr>
                                    <tr>
                                        <td>SMTP Provider</td>
                                        <td id="logInspectSmtpProvider">${response.data.smtp_provider}</td>
                                    </tr>
                                    <tr>
                                        <td>MX Found</td>
                                        <td id="logInspectMxFound">${response.data.mx_found}</td>
                                    </tr>
                                    <tr>
                                        <td>MX Record</td>
                                        <td id="logInspectMxRecord">${response.data.mx_record}</td>
                                    </tr>
                                    <tr>
                                        <td>Firstname</td>
                                        <td id="logInspectFirstname">${response.data.firstname}</td>
                                    </tr>
                                    <tr>
                                        <td>Lastname</td>
                                        <td id="logInspectLastname">${response.data.lastname}</td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td id="logInspectGender">${response.data.gender}</td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td id="logInspectCountry">${response.data.country}</td>
                                    </tr>
                                    <tr>
                                        <td>Region</td>
                                        <td id="logInspectRegion">${response.data.region}</td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td id="logInspectCity">${response.data.city}</td>
                                    </tr>
                                    <tr>
                                        <td>ZIP Code</td>
                                        <td id="logInspectZipCode">${response.data.zipcode}</td>
                                    </tr>
                                    <tr>
                                        <td>Processed At</td>
                                        <td id="logInspectProcessedAt">${response.data.processed_at}</td>
                                    </tr>
                                </tbody>
                            </table>`);
                        } else {
                            $("#logInspectStatus").text(response.data.status);
                            $("#logInspectSubStatus").text(response.data.sub_status);
                            $("#logInspectFreeEmail").text(response.data.free_email);
                            $("#logInspectDyM").text(response.data.did_you_mean);
                            $("#logInspectAccount").text(response.data.account);
                            $("#logInspectDomain").text(response.data.domain);
                            $("#logInspectDomainAge").text(response.data.domain_age_days);
                            $("#logInspectSmtpProvider").text(response.data.smtp_provider);
                            $("#logInspectMxFound").text(response.data.mx_found);
                            $("#logInspectMxRecord").text(response.data.mx_record);
                            $("#logInspectFirstname").text(response.data.firstname);
                            $("#logInspectLastname").text(response.data.lastname);
                            $("#logInspectGender").text(response.data.gender);
                            $("#logInspectCountry").text(response.data.country);
                            $("#logInspectRegion").text(response.data.region);
                            $("#logInspectCity").text(response.data.city);
                            $("#logInspectZipCode").text(response.data.zipcode);
                            $("#logInspectProcessedAt").text(response.data.processed_at);
                        }

                        $("#logInspectModalLabel").text(`ZeroBounce Log #${data.id}`);

                        $("#logInspectModal").modal('show');
                    })
                    .fail(function (jqXHR, textStatus) {
                        console.log(jqXHR.responseJSON.data.reason);
                    })
                    .always(function () {
                    });
            });
        }

        $('#csvUpload').on('change', function () {
            let fileName = $(this).val().split('\\').pop();
            $('#fileName').text(fileName);
            $('#fileNameDisplay').css('display', 'flex');
            $('#manual-upload').attr('disabled', true);
        });

        // Remove file and reset input
        $('#removeFileBtn').on('click', function () {
            $('#csvUpload').val('');
            $('#fileNameDisplay').hide();
            $('#manual-upload').attr('disabled', false);
        });

        const setupTextAreaResults = (form, data) => {
            const emailBatch = data.email_batch;
            const tableBody = $('#resultsTable tbody');

            tableBody.empty();

            emailBatch.forEach(function (email) {
                const newRow = '<tr>' +
                    '<td>' + email.address + '</td>' +
                    '<td class="text-' + (email.status === 'valid' ? 'success' : 'danger') + ' text-center">' + email.status.charAt(0).toUpperCase() + email.status.slice(1) + '</td>' +
                    '<td class="text-' + (email.sub_status ? 'info' : 'muted') + ' text-center">' + (email.sub_status ? email.sub_status.charAt(0).toUpperCase() + email.sub_status.slice(1) : '&mdash;') + '</td>' +
                    '</tr>';

                tableBody.append(newRow);
            });
            $(form).find('textarea').val('');
        }

        const setupFileResults = (form) => {
            $(form).find("#removeFileBtn").click();
            $('#successModal .modal-body').text('File upload complete.');
            $('#successModal').modal('show');
            getStatus();
        }

        $("#bulk-validation-form, #bulk-validation-form-manual").submit((event) => {
            event.preventDefault();
            const form = event.target;
            const loader = event.target.querySelectorAll('.zb-loader-overlay')[0];
            const formData = new FormData(form);

            $(loader).fadeIn();

            formData.append('action', 'zerobounce_batch_email_validation');
            formData.append('nonce', params.ajax_batch_validation_nonce);

            $.ajax({
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                url: params.ajax_url,
                type: 'POST',
                success: function (response) {
                    if (response.type === "manual" && response.errors && response.errors.length === 0) {
                        setupTextAreaResults(form, response);
                    } else if (response.type === "file" && response.success) {
                        setupFileResults(form);
                    }

                    if (response.success === false || response.errors && response.errors.length > 0) {
                        let errorMessage = response.type === "file" ? response.error : response.data.error ?? 'An error occurred during the request. Please try again later.<br/>If the issue persists please contact our support team <a href="mailto:support@zerobounce.net">support@zerobounce.net</a>';

                        $('#errorModal .modal-body').html(errorMessage);
                        $('#errorModal').modal('show');
                    }

                    $(loader).fadeOut();
                },
                error: function (xhr, status, error) {
                    $(loader).fadeOut();
                    $('#errorModal .modal-body').text('Error: ' + status + '. ' + error);
                    $('#errorModal').modal('show');
                    console.log('AJAX Error: ', error);
                },
            });
        });

        const generateDownloadLink = (file_id) => {
            return params.ajax_url + '?action=zerobounce_validated_emails_download&file_id=' + file_id + '&nonce=' + params.ajax_download_validated_file_nonce;
        }

        const displayFileValidationStatus = (data) => {
            const tableBody = $('#csv-results tbody');

            tableBody.empty();

            data.forEach(({file_name, file_id, validation_status, created_at}) => {
                let newRow = '<tr>' +
                    '<td>' + file_name + '</td>' +
                    '<td class="text-center">' + created_at.slice(0, created_at.length - 3) + '</td>' +
                    '<td class="text-center">' + (validation_status === '100%' ? '<a href="' + generateDownloadLink(file_id) + '">Download</a>' : validation_status) + '</td>' +
                    '</tr>';
                tableBody.append(newRow);
            });
        }

        const getStatus = (page) => {
            $("#zb-results-loader").show();

            let data = {
                'action': 'zerobounce_get_uploaded_file_data',
                'nonce': params.ajax_get_files_info_nonce,
                'page': page
            };

            $.ajax({
                url: params.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        displayFileValidationStatus(response.data);
                        updatePagination(response.pagination)
                    } else {
                        console.error('Error:', response.data.error);
                    }

                    $("#zb-results-loader").hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error: ' + textStatus, errorThrown);
                }
            });
        }

        const updatePagination = (pagination) => {
            let paginationHtml = '';

            const maxVisiblePages = 5;
            let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(pagination.total_pages, pagination.current_page + Math.floor(maxVisiblePages / 2));

            if (endPage - startPage < maxVisiblePages - 1) {
                if (pagination.current_page <= Math.floor(maxVisiblePages / 2)) {
                    endPage = Math.min(pagination.total_pages, startPage + maxVisiblePages - 1);
                } else if (pagination.total_pages - pagination.current_page < Math.floor(maxVisiblePages / 2)) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }
            }

            if (pagination.current_page > 1) {
                paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>`;
            } else {
                paginationHtml += `
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>`;
            }

            if (startPage > 1) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                if (startPage > 2) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === Number(pagination.current_page)) {
                    paginationHtml += `<li class="page-item active"><a class="page-link" href="#"  aria-disabled="true" data-page="${i}">${i}</a></li>`;
                } else {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
            }

            if (endPage < pagination.total_pages) {
                if (endPage < pagination.total_pages - 1) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.total_pages}">${pagination.total_pages}</a></li>`;
            }

            if (pagination.current_page < pagination.total_pages) {
                paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>`;
            } else {
                paginationHtml += `
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>`;
            }

            $('#pagination').html(paginationHtml);

            $('.page-link').click(function (e) {
                e.preventDefault();
                if ($(this).parent().hasClass('active')) {
                    return;
                }

                let page = $(this).data('page');
                getStatus(page);
            });
        }
        getStatus(1);
    });
})(jQuery);