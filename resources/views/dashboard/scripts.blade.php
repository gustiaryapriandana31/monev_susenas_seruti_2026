@push('scripts')
<script>
    var petugasOptions = null;
    var petugasOptionsRequest = null;
    var dataTables = {};

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(char) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char];
        });
    }

    function buildOptionsHtml(list, placeholder) {
        let html = `<option value="">${escapeHtml(placeholder)}</option>`;
        list.forEach(function(p) {
            html += `<option value="${escapeHtml(p.kode)}">${escapeHtml(p.nama)}</option>`;
        });
        return html;
    }

    function withPetugasOptions(callback) {
        if (petugasOptions) {
            if (typeof callback === 'function') callback(petugasOptions);
            return;
        }
        if (!petugasOptionsRequest) {
            petugasOptionsRequest = $.getJSON('{{ route("dashboard.petugas_options") }}')
                .done(function(data) { petugasOptions = data; })
                .fail(function() {
                    petugasOptionsRequest = null;
                    Swal.fire('Error', 'Gagal memuat daftar petugas', 'error');
                });
        }
        if (typeof callback === 'function') {
            petugasOptionsRequest.done(callback);
        }
    }

    function petugasConfig(field) {
        var configs = {
            petugas_ppl: { key: 'ppl', label: '- PPL -', modalLabel: '-- Pilih PPL --' },
            petugas_pml: { key: 'pml', label: '- PML -', modalLabel: '-- Pilih PML --' },
            petugas_entry: { key: 'entry', label: '- Entry -', modalLabel: '-- Pilih Entry --' },
            petugas_susenas: { key: 'entry', label: '- Susenas -', modalLabel: '-- Pilih Susenas --' },
            petugas_seruti: { key: 'entry', label: '- Seruti -', modalLabel: '-- Pilih Seruti --' }
        };
        return configs[field] || { key: 'entry', label: '-', modalLabel: '-- Pilih Petugas --' };
    }

    function renderPetugasCell(row, field, type) {
        var cfg = petugasConfig(field);
        var selected = row[field] || '';
        var displayName = row[field + '_nama'] || cfg.label;
        return '<button type="button" class="petugas-inline-trigger text-[10px] font-bold bg-white/50 border border-white/60 rounded-xl px-2 py-1 w-full text-left focus:ring-bps-orange flex items-center justify-between gap-2"'
            + ' data-id="' + escapeHtml(row.id) + '"'
            + ' data-type="' + escapeHtml(type) + '"'
            + ' data-field="' + escapeHtml(field) + '"'
            + ' data-selected="' + escapeHtml(selected) + '">'
            + '<span class="truncate">' + escapeHtml(displayName) + '</span>'
            + '<i class="fa-solid fa-chevron-down text-[9px] text-gray-400"></i>'
            + '</button>';
    }

    function closeModal(id) {
        $('#' + id).fadeOut(200);
    }

    function populateModalSelects(modalId, fields, values) {
        withPetugasOptions(function(options) {
            fields.forEach(function(field) {
                var cfg = petugasConfig(field.name);
                $('#' + field.id)
                    .html(buildOptionsHtml(options[cfg.key] || [], cfg.modalLabel))
                    .val(values[field.name] || '');
            });
            $('#' + modalId).removeClass('hidden').css('display', 'flex').hide().fadeIn(200);
        });
    }

    function hostRow($el) {
        var $tr = $el.closest('tr');
        if ($tr.hasClass('child')) $tr = $tr.prev();
        return $tr;
    }

    function dataTableRow($el, type) {
        if (!dataTables[type]) return null;
        var row = dataTables[type].row(hostRow($el));
        return row && row.any() ? row : null;
    }

    function editDssls(button) {
        var $btn = $(button);
        var item = $btn.attr('data-item') ? JSON.parse($btn.attr('data-item')) : null;
        if (!item) {
            var row = dataTableRow($btn, 'dssls');
            item = row ? row.data() : null;
        }
        if (!item) return;
        $('#dssls-id').val(item.id);
        $('#dssls-jml-kel').val(item.perkiraan_jumlah_keluarga);
        $('#dssls-sampel').val(item.sampel_seruti);
        populateModalSelects('modal-dssls', [
            { id: 'dssls-ppl', name: 'petugas_ppl' },
            { id: 'dssls-pml', name: 'petugas_pml' },
            { id: 'dssls-entry', name: 'petugas_entry' }
        ], item);
    }

    function editDsrt(button) {
        var $btn = $(button);
        var item = $btn.attr('data-item') ? JSON.parse($btn.attr('data-item')) : null;
        if (!item) {
            var row = dataTableRow($btn, 'dsrt');
            item = row ? row.data() : null;
        }
        if (!item) return;
        $('#dsrt-id').val(item.id);
        $('#dsrt-r503').val(item.r503);
        $('#dsrt-r503b').val(item.r503b);
        populateModalSelects('modal-dsrt', [
            { id: 'dsrt-ppl', name: 'petugas_ppl' },
            { id: 'dsrt-pml', name: 'petugas_pml' },
            { id: 'dsrt-susenas', name: 'petugas_susenas' },
            { id: 'dsrt-seruti', name: 'petugas_seruti' }
        ], item);
    }

    var deleteUrlMap = { dssls: '/data-dssls', dsrt: '/data-dsrt', lapangan: '/petugas-lapangan', entry: '/petugas-entry' };
    var selectAllMap = { dssls: '#selectAllDssls', dsrt: '#selectAllDsrt', lapangan: '#selectAllLapangan', entry: '#selectAllEntry' };

    function reloadTable(type) {
        if (dataTables[type]) {
            dataTables[type].ajax.reload(null, false);
            $(selectAllMap[type]).prop('checked', false);
        }
    }

    function deleteSelected(type) {
        var ids = [];
        $('.row-' + type + '-checkbox:checked').each(function() { ids.push($(this).val()); });
        if (ids.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal satu data', 'warning');
            return;
        }
        Swal.fire({
            title: 'Hapus data terpilih?',
            text: 'Anda akan menghapus ' + ids.length + ' data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.post(deleteUrlMap[type] + '/delete-bulk', { ids: ids }, function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', response.message, 'success');
                        reloadTable(type);
                    }
                });
            }
        });
    }

    function deleteAll(type) {
        Swal.fire({
            title: 'Reset semua data?',
            text: 'Seluruh data pada tabel ini akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Reset Semua!'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.post(deleteUrlMap[type] + '/delete-all', function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', response.message, 'success');
                        reloadTable(type);
                    }
                });
            }
        });
    }

    function renderActionButton(type) {
        var fn = type === 'dssls' ? 'editDssls' : 'editDsrt';
        return '<button type="button" onclick="' + fn + '(this)" class="w-8 h-8 rounded-lg bg-bps-orange/10 text-bps-orange hover:bg-bps-orange hover:text-white transition-all"><i class="fa-solid fa-pen-to-square"></i></button>';
    }

    function renderCheckbox(row, type) {
        var val = (type === 'lapangan' || type === 'entry') ? row.kode_petugas : row.id;
        return '<input type="checkbox" class="row-' + type + '-checkbox w-4 h-4 rounded-md border-gray-300" value="' + escapeHtml(val) + '">';
    }

    function renderStatusCheckbox(row, type, field) {
        var timeField = 'waktu_' + field;
        var labelKey = field.replace('ceklis_', '');
        var checked = row[field] ? 'checked' : '';
        return '<div class="flex flex-col items-center">'
            + '<input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer"'
            + ' data-id="' + escapeHtml(row.id) + '" data-type="' + escapeHtml(type) + '" data-field="' + escapeHtml(field) + '" ' + checked + '>'
            + '<span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-' + labelKey + '-' + type + '-' + escapeHtml(row.id) + '">'
            + escapeHtml(row[timeField] || '') + '</span></div>';
    }

    function renderPlainText(data) {
        return '<span class="text-xs font-medium">' + escapeHtml(data) + '</span>';
    }

    function lapanganColumns() {
        return [
            { data: null, orderable: false, searchable: false, className: 'text-center', render: function(d, t, row) { return renderCheckbox(row, 'lapangan'); } },
            { data: 'kode_petugas', render: function(d) { return '<span class="font-bold text-bps-dark text-xs">' + escapeHtml(d) + '</span>'; } },
            { data: 'provinsi', render: renderPlainText },
            { data: 'kabupaten', render: renderPlainText },
            { data: 'nama_petugas', render: function(d) { return '<span class="font-medium text-xs">' + escapeHtml(d) + '</span>'; } },
            { data: 'no_hp', render: renderPlainText },
            { data: 'jabatan', render: renderPlainText },
            { data: 'status', render: function(d) { return '<span class="px-2 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold uppercase">' + escapeHtml(d) + '</span>'; } }
        ];
    }

    function entryColumns() {
        return [
            { data: null, orderable: false, searchable: false, className: 'text-center', render: function(d, t, row) { return renderCheckbox(row, 'entry'); } },
            { data: 'kode_petugas', render: function(d) { return '<span class="font-bold text-bps-dark text-xs">' + escapeHtml(d) + '</span>'; } },
            { data: 'provinsi', render: renderPlainText },
            { data: 'kabupaten', render: renderPlainText },
            { data: 'nama_petugas', render: function(d) { return '<span class="font-medium text-xs">' + escapeHtml(d) + '</span>'; } },
            { data: 'email', render: renderPlainText },
            { data: 'no_hp', render: renderPlainText },
            { data: 'status', render: function(d) { return '<span class="px-2 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold uppercase">' + escapeHtml(d) + '</span>'; } }
        ];
    }

    function dsslsColumns() {
        return [
            { data: null, orderable: false, searchable: false, className: 'text-center', render: function(d, t, row) { return renderCheckbox(row, 'dssls'); } },
            { data: null, orderable: false, searchable: false, render: function() { return renderActionButton('dssls'); } },
            { data: 'nama_kecamatan', render: function(d, t, row) {
                return '<p class="font-bold text-bps-dark text-xs">' + escapeHtml(row.nama_kecamatan) + '</p>'
                    + '<p class="text-[10px] text-gray-500 uppercase">' + escapeHtml(row.nama_desa_kelurahan) + '</p>';
            } },
            { data: 'kode_sls', render: function(d, t, row) {
                return '<p class="font-bold text-bps-dark text-xs">' + escapeHtml(row.kode_sls) + '</p>'
                    + '<p class="text-[10px] text-gray-500 uppercase truncate max-w-[150px]">' + escapeHtml(row.nama_sls) + '</p>';
            } },
            { data: 'ceklis_lap', className: 'text-center', render: function(d, t, row) { return renderStatusCheckbox(row, 'dssls', 'ceklis_lap'); } },
            { data: 'ceklis_sosial', className: 'text-center', render: function(d, t, row) { return renderStatusCheckbox(row, 'dssls', 'ceklis_sosial'); } },
            { data: 'ceklis_ipds', className: 'text-center', render: function(d, t, row) { return renderStatusCheckbox(row, 'dssls', 'ceklis_ipds'); } },
            { data: 'petugas_ppl_nama', render: function(d, t, row) { return renderPetugasCell(row, 'petugas_ppl', 'dssls'); } },
            { data: 'petugas_pml_nama', render: function(d, t, row) { return renderPetugasCell(row, 'petugas_pml', 'dssls'); } },
            { data: 'petugas_entry_nama', render: function(d, t, row) { return renderPetugasCell(row, 'petugas_entry', 'dssls'); } }
        ];
    }

    function dsrtColumns() {
        return [
            { data: null, orderable: false, searchable: false, className: 'text-center', render: function(d, t, row) { return renderCheckbox(row, 'dsrt'); } },
            { data: null, orderable: false, searchable: false, render: function() { return renderActionButton('dsrt'); } },
            { data: 'kec', render: function(d, t, row) {
                return '<p class="font-bold text-bps-dark text-xs">' + escapeHtml(row.nmkec || row.kec) + '</p>'
                    + '<p class="text-[10px] text-gray-500 uppercase">' + escapeHtml(row.nmdesa || row.desa) + '</p>';
            } },
            { data: 'nmslsm', render: function(d, t, row) {
                return '<p class="font-bold text-bps-dark text-xs truncate max-w-[100px]">' + escapeHtml(row.nmslsm) + '</p>'
                    + '<p class="text-[10px] text-gray-500 uppercase">' + escapeHtml(row.nks_sak22) + '</p>';
            } },
            { data: 'r503', className: 'font-medium text-xs', render: function(d) { return escapeHtml(d); } },
            { data: 'ceklis_lap', className: 'text-center', render: function(d, t, row) { return renderStatusCheckbox(row, 'dsrt', 'ceklis_lap'); } },
            { data: 'ceklis_sosial', className: 'text-center', render: function(d, t, row) { return renderStatusCheckbox(row, 'dsrt', 'ceklis_sosial'); } },
            { data: 'ceklis_ipds', className: 'text-center', render: function(d, t, row) { return renderStatusCheckbox(row, 'dsrt', 'ceklis_ipds'); } },
            { data: 'petugas_ppl_nama', render: function(d, t, row) { return renderPetugasCell(row, 'petugas_ppl', 'dsrt'); } },
            { data: 'petugas_pml_nama', render: function(d, t, row) { return renderPetugasCell(row, 'petugas_pml', 'dsrt'); } },
            { data: 'petugas_susenas_nama', render: function(d, t, row) { return renderPetugasCell(row, 'petugas_susenas', 'dsrt'); } },
            { data: 'petugas_seruti_nama', render: function(d, t, row) { return renderPetugasCell(row, 'petugas_seruti', 'dsrt'); } }
        ];
    }

    function columnsFor(section) {
        return { lapangan: lapanganColumns, entry: entryColumns, dssls: dsslsColumns, dsrt: dsrtColumns }[section]();
    }

    $(document).ready(function() {
        var processingHtml = '<div class="custom-loader flex items-center justify-center space-x-1.5">'
            + '<div class="w-2.5 h-2.5 bg-[#FF8C00] rounded-full animate-bounce" style="animation-delay: -0.3s"></div>'
            + '<div class="w-2.5 h-2.5 bg-[#FF8C00] rounded-full animate-bounce" style="animation-delay: -0.15s"></div>'
            + '<div class="w-2.5 h-2.5 bg-[#FF8C00] rounded-full animate-bounce"></div>'
            + '</div>';

        var dtConfigs = {
            responsive: false,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            searchDelay: 350,
            language: {
                search: '',
                searchPlaceholder: 'Cari data...',
                lengthMenu: '_MENU_ entries',
                processing: processingHtml,
                paginate: { previous: '<i class="fa-solid fa-chevron-left"></i>', next: '<i class="fa-solid fa-chevron-right"></i>' }
            },
            drawCallback: function() {
                $('.dataTables_paginate .paginate_button').addClass('rounded-xl border-0');
            }
        };

        var serverEndpoints = {
            lapangan: '{{ route("dashboard.datatable_lapangan") }}',
            entry: '{{ route("dashboard.datatable_entry") }}',
            dssls: '{{ route("dashboard.datatable_dssls") }}',
            dsrt: '{{ route("dashboard.datatable_dsrt") }}'
        };

        var defaultOrders = { lapangan: [[7, 'asc']], entry: [[7, 'asc']], dssls: [[2, 'asc']], dsrt: [[2, 'asc']] };

        function initServerTable(section) {
            if (dataTables[section]) {
                dataTables[section].columns.adjust();
                return;
            }
            $('#tbody-' + section).empty();
            dataTables[section] = $('#dt-' + section).DataTable($.extend(true, {}, dtConfigs, {
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax: serverEndpoints[section],
                order: defaultOrders[section],
                columns: columnsFor(section)
            }));
        }

        // Pre-fetch petugas options immediately (fire-and-forget)
        withPetugasOptions();

        // Initialize the default active table directly (no fake click)
        var activeSection = '{{ session("active_tab", "lapangan") }}';
        initServerTable(activeSection);

        // Tab switching
        $(document).on('tabChanged', function(e, section) {
            initServerTable(section);
        });

        // Inline petugas dropdown: single-click opens select
        $(document).on('click', '.petugas-inline-trigger', function() {
            var $button = $(this);
            var type = $button.data('type');
            var field = $button.data('field');
            var row = dataTableRow($button, type);
            if (!row) return;

            withPetugasOptions(function(options) {
                var rowData = row.data();
                var cfg = petugasConfig(field);
                var selected = rowData[field] || '';
                var selectClass = type === 'dssls' ? 'petugas-dssls-update' : 'petugas-dsrt-update';
                var list = options[cfg.key] || [];
                var html = '<select class="' + selectClass + ' inline-petugas-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange"'
                    + ' data-id="' + escapeHtml(rowData.id) + '"'
                    + ' data-type="' + escapeHtml(type) + '"'
                    + ' data-field="' + escapeHtml(field) + '"'
                    + ' data-selected="' + escapeHtml(selected) + '">'
                    + buildOptionsHtml(list, cfg.label) + '</select>';

                var $cell = $button.closest('td');
                $cell.html(html);
                var selectEl = $cell.find('select')[0];
                selectEl.value = selected;
                selectEl.focus();
                // Try to open dropdown immediately (Chrome 114+, Firefox 127+)
                try { selectEl.showPicker(); } catch(e) {
                    // Fallback: simulate mousedown to open
                    var evt = new MouseEvent('mousedown', { bubbles: true, cancelable: true, view: window });
                    selectEl.dispatchEvent(evt);
                }
            });
        });

        // Revert select back to button on blur (if no change)
        $(document).on('blur', '.inline-petugas-select', function() {
            var $sel = $(this);
            var type = $sel.data('type');
            var field = $sel.data('field');
            var row = dataTableRow($sel, type);
            if (row) {
                var rowData = row.data();
                $sel.closest('td').html(renderPetugasCell(rowData, field, type));
            }
        });

        // Select all checkboxes
        $(document).on('change', '#selectAllLapangan', function() {
            $('.row-lapangan-checkbox').prop('checked', $(this).is(':checked'));
        });
        $(document).on('change', '#selectAllEntry', function() {
            $('.row-entry-checkbox').prop('checked', $(this).is(':checked'));
        });
        $(document).on('change', '#selectAllDssls', function() {
            $('.row-dssls-checkbox').prop('checked', $(this).is(':checked'));
        });
        $(document).on('change', '#selectAllDsrt', function() {
            $('.row-dsrt-checkbox').prop('checked', $(this).is(':checked'));
        });

        // Inline petugas update
        $(document).on('change', '.petugas-dssls-update, .petugas-dsrt-update', function() {
            var $sel = $(this);
            var selectedValue = $sel.val();
            var selectedText = selectedValue ? $sel.find('option:selected').text() : null;
            var id = $sel.data('id');
            var field = $sel.data('field');
            var type = $sel.data('type') || ($sel.hasClass('petugas-dssls-update') ? 'dssls' : 'dsrt');
            var row = dataTableRow($sel, type);

            $sel.css('opacity', '0.5');
            $.post('/data-' + type + '/update-inline', { id: id, field: field, value: selectedValue }, function(response) {
                if (!response.success) {
                    Swal.fire('Error', response.message || 'Gagal menyimpan petugas', 'error');
                    return;
                }
                if (row) {
                    var rowData = row.data();
                    rowData[field] = selectedValue;
                    rowData[field + '_nama'] = selectedText;
                    row.data(rowData);
                    $sel.closest('td').html(renderPetugasCell(rowData, field, type));
                }
            }).fail(function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            }).always(function() {
                $sel.css('opacity', '1');
            });
        });

        // Ceklis toggle
        $(document).on('change', '.ceklis-toggle', function() {
            var $cb = $(this);
            var isChecked = $cb.is(':checked') ? 1 : 0;
            var id = $cb.data('id');
            var type = $cb.data('type');
            var field = $cb.data('field');
            var row = dataTableRow($cb, type);

            $cb.parent().css('opacity', '0.5');
            $.post('/data-' + type + '/toggle-ceklis', { id: id, field: field, state: isChecked }, function(response) {
                if (!response.success) {
                    Swal.fire('Error', 'Gagal update status ceklis', 'error');
                    return;
                }
                var timeLabelId = '#lbl-' + field.replace('ceklis_', '') + '-' + type + '-' + id;
                var timestamp = response.timestamp || '';
                $(timeLabelId).text(timestamp);
                if (row) {
                    var rowData = row.data();
                    rowData[field] = Boolean(isChecked);
                    rowData['waktu_' + field] = timestamp;
                    row.data(rowData);
                }
            }).fail(function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            }).always(function() {
                $cb.parent().css('opacity', '1');
            });
        });
    });
</script>
@endpush
