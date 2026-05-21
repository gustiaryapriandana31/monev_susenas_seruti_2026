@push('scripts')
<script>
    // ─── Role flags (rendered server-side, cannot be tampered from client) ──────
    var isSuperAdmin  = {{ Auth::user()->isSuperAdmin()  ? 'true' : 'false' }};
    var isAdminIpds   = {{ Auth::user()->isAdminIpds()   ? 'true' : 'false' }};
    var isAdminSosial = {{ Auth::user()->isAdminSosial() ? 'true' : 'false' }};
    // ─────────────────────────────────────────────────────────────────────────────

    var petugasOptions        = null;
    var petugasOptionsRequest = null;
    var dataTables            = {};

    // ── Helpers ─────────────────────────────────────────────────────────────────

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(c) {
            return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[c];
        });
    }

    function buildOptionsHtml(list, placeholder) {
        var html = '<option value="">' + escapeHtml(placeholder) + '</option>';
        list.forEach(function(p) {
            html += '<option value="' + escapeHtml(p.kode) + '">' + escapeHtml(p.nama) + '</option>';
        });
        return html;
    }

    function withPetugasOptions(callback) {
        if (petugasOptions) { if (typeof callback === 'function') callback(petugasOptions); return; }
        if (!petugasOptionsRequest) {
            petugasOptionsRequest = $.getJSON('{{ route("dashboard.petugas_options") }}')
                .done(function(data) { petugasOptions = data; })
                .fail(function() { petugasOptionsRequest = null; Swal.fire('Error','Gagal memuat daftar petugas','error'); });
        }
        if (typeof callback === 'function') petugasOptionsRequest.done(callback);
    }

    function petugasConfig(field) {
        var configs = {
            petugas_ppl:     { key: 'ppl',   label: '- PPL -',    modalLabel: '-- Pilih PPL --' },
            petugas_pml:     { key: 'pml',   label: '- PML -',    modalLabel: '-- Pilih PML --' },
            petugas_entry:   { key: 'entry', label: '- Entry -',   modalLabel: '-- Pilih Entry --' },
            petugas_susenas: { key: 'entry', label: '- Susenas -', modalLabel: '-- Pilih Susenas --' },
            petugas_seruti:  { key: 'entry', label: '- Seruti -',  modalLabel: '-- Pilih Seruti --' }
        };
        return configs[field] || { key: 'entry', label: '-', modalLabel: '-- Pilih Petugas --' };
    }

    // ── Cell Renderers ───────────────────────────────────────────────────────────

    /** Interactive petugas dropdown button */
    function renderPetugasCell(row, field, type) {
        var cfg         = petugasConfig(field);
        var selected    = row[field] || '';
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

    /** Plain-text read-only cell for petugas */
    function renderPetugasReadonly(row, field) {
        var cfg         = petugasConfig(field);
        var displayName = row[field + '_nama'] || cfg.label;
        return '<span class="text-xs font-medium text-gray-500">' + escapeHtml(displayName) + '</span>';
    }

    /** Disabled ceklis icon (view-only) with timestamp */
    function renderCeklisIcon(row, field) {
        var isChecked = row[field];
        var timeField = 'waktu_' + field;
        var icon      = isChecked
            ? '<i class="fa-solid fa-check-circle text-green-500 text-base"></i>'
            : '<i class="fa-regular fa-circle text-gray-300 text-base"></i>';
        return '<div class="flex flex-col items-center gap-0.5">'
            + icon
            + '<span class="text-[8px] font-bold text-gray-400 uppercase">' + escapeHtml(row[timeField] || '') + '</span>'
            + '</div>';
    }

    /** Interactive ceklis checkbox */
    function renderStatusCheckbox(row, type, field) {
        var timeField = 'waktu_' + field;
        var labelKey  = field.replace('ceklis_', '');
        var checked   = row[field] ? 'checked' : '';
        return '<div class="flex flex-col items-center">'
            + '<input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer"'
            + ' data-id="' + escapeHtml(row.id) + '" data-type="' + escapeHtml(type) + '" data-field="' + escapeHtml(field) + '" ' + checked + '>'
            + '<span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-' + labelKey + '-' + type + '-' + escapeHtml(row.id) + '">'
            + escapeHtml(row[timeField] || '') + '</span></div>';
    }

    var r203StatusOptions = [
        { kode: '1', nama: 'Terisi Lengkap' },
        { kode: '2', nama: 'Terisi tdk lengkap' },
        { kode: '3', nama: 'Tidak ada ART/responden yang memberikan informasi sampai akhir masa pencacahan' },
        { kode: '4', nama: 'menolak' },
        { kode: '5', nama: 'Ruta pindah' }
    ];

    /**
     * Enum cell (r203 status).
     * @param readOnly  - jika true, render plain text
     */
    function renderEnumCell(row, field, readOnly) {
        var selected     = row[field] || '';
        var displayLabel = '- Pilih Status -';
        if (selected) {
            var found = r203StatusOptions.find(function(o) { return o.kode == selected; });
            if (found) displayLabel = found.nama;
        }
        if (readOnly) {
            return '<span class="text-xs font-medium">' + escapeHtml(displayLabel) + '</span>';
        }
        return '<button type="button" class="enum-inline-trigger text-[10px] font-bold bg-white/50 border border-white/60 rounded-xl px-2 py-1 w-full text-left focus:ring-bps-orange flex items-center justify-between gap-2 max-w-[150px]"'
            + ' data-id="' + escapeHtml(row.id) + '" data-field="' + escapeHtml(field) + '" data-selected="' + escapeHtml(selected) + '">'
            + '<span class="truncate">' + escapeHtml(displayLabel) + '</span>'
            + '<i class="fa-solid fa-chevron-down text-[9px] text-gray-400"></i>'
            + '</button>';
    }

    /**
     * Bool cell (Ya/Tidak).
     * @param readOnly  - jika true, render plain text
     */
    function renderBoolCell(row, field, readOnly) {
        var selected     = row[field];
        var displayLabel = '- Pilih -';
        if (selected === true  || selected === 1 || selected === '1' || selected === 'true')  displayLabel = 'YA';
        if (selected === false || selected === 0 || selected === '0' || selected === 'false') displayLabel = 'TIDAK';
        if (readOnly) {
            return '<span class="text-xs font-medium">' + escapeHtml(displayLabel) + '</span>';
        }
        return '<button type="button" class="bool-inline-trigger text-[10px] font-bold bg-white/50 border border-white/60 rounded-xl px-2 py-1 w-full text-left focus:ring-bps-orange flex items-center justify-between gap-2 max-w-[100px]"'
            + ' data-id="' + escapeHtml(row.id) + '" data-field="' + escapeHtml(field) + '"'
            + ' data-selected="' + (selected === null || selected === undefined ? '' : (selected ? '1' : '0')) + '">'
            + '<span class="truncate">' + escapeHtml(displayLabel) + '</span>'
            + '<i class="fa-solid fa-chevron-down text-[9px] text-gray-400"></i>'
            + '</button>';
    }

    /**
     * Int cell (inline number input).
     * @param readOnly  - jika true, render plain text
     */
    function renderIntCell(row, field, type, readOnly) {
        var value        = row[field];
        var displayValue = (value === null || value === undefined || value === '') ? '-' : escapeHtml(value);
        if (readOnly) {
            return '<span class="text-xs font-semibold text-bps-dark">' + displayValue + '</span>';
        }
        return '<div class="flex items-center justify-between gap-2 px-2 py-1 border border-transparent rounded-xl hover:bg-gray-50/50 cursor-pointer group int-inline-trigger"'
            + ' data-id="' + escapeHtml(row.id) + '" data-field="' + escapeHtml(field) + '" data-type="' + escapeHtml(type || 'dsrt') + '" data-value="' + escapeHtml(value || '') + '">'
            + '<span class="text-xs font-semibold text-bps-dark">' + displayValue + '</span>'
            + '<i class="fa-solid fa-pen text-[10px] text-gray-400 hover:text-bps-orange opacity-0 group-hover:opacity-100 transition-opacity"></i>'
            + '</div>';
    }

    function renderPlainText(data) {
        return '<span class="text-xs font-medium">' + escapeHtml(data) + '</span>';
    }

    function renderCheckbox(row, type) {
        var val = (type === 'lapangan' || type === 'entry') ? row.kode_petugas : row.id;
        return '<input type="checkbox" class="row-' + type + '-checkbox w-4 h-4 rounded-md border-gray-300" value="' + escapeHtml(val) + '">';
    }

    function renderActionButton(type) {
        var fn = type === 'dssls' ? 'editDssls' : 'editDsrt';
        return '<button type="button" onclick="' + fn + '(this)" class="w-8 h-8 rounded-lg bg-bps-orange/10 text-bps-orange hover:bg-bps-orange hover:text-white transition-all"><i class="fa-solid fa-pen-to-square"></i></button>';
    }

    // ── Modal helpers ────────────────────────────────────────────────────────────

    function closeModal(id) { $('#' + id).fadeOut(200); }

    function populateModalSelects(modalId, fields, values) {
        withPetugasOptions(function(options) {
            fields.forEach(function(f) {
                var cfg = petugasConfig(f.name);
                $('#' + f.id).html(buildOptionsHtml(options[cfg.key] || [], cfg.modalLabel)).val(values[f.name] || '');
            });
            $('#' + modalId).removeClass('hidden').css('display','flex').hide().fadeIn(200);
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
        if (isAdminIpds) return; // adminipds: tidak ada action button di dssls
        var $btn = $(button);
        var item = $btn.attr('data-item') ? JSON.parse($btn.attr('data-item')) : null;
        if (!item) { var row = dataTableRow($btn, 'dssls'); item = row ? row.data() : null; }
        if (!item) return;
        $('#dssls-id').val(item.id);
        $('#dssls-jml-kel').val(item.perkiraan_jumlah_keluarga);
        $('#dssls-sampel').val(item.sampel_seruti);
        // Build modal field list based on role
        var fields = [];
        if (isSuperAdmin || isAdminSosial) {
            fields.push({ id: 'dssls-ppl', name: 'petugas_ppl' });
            fields.push({ id: 'dssls-pml', name: 'petugas_pml' });
        }
        if (isSuperAdmin) {
            fields.push({ id: 'dssls-entry', name: 'petugas_entry' });
        }
        populateModalSelects('modal-dssls', fields, item);
    }

    function editDsrt(button) {
        var $btn = $(button);
        var item = $btn.attr('data-item') ? JSON.parse($btn.attr('data-item')) : null;
        if (!item) { var row = dataTableRow($btn, 'dsrt'); item = row ? row.data() : null; }
        if (!item) return;
        $('#dsrt-id').val(item.id);

        if (isAdminIpds) {
            // adminipds: hanya tampilkan modal notice saja (form sudah disembunyikan di Blade)
            $('#modal-dsrt').removeClass('hidden').css('display','flex').hide().fadeIn(200);
            return;
        }

        $('#dsrt-r503').val(item.r503);
        $('#dsrt-r503b').val(item.r503b);
        var fields = [
            { id: 'dsrt-ppl', name: 'petugas_ppl' },
            { id: 'dsrt-pml', name: 'petugas_pml' }
        ];
        if (isSuperAdmin) {
            fields.push({ id: 'dsrt-susenas', name: 'petugas_susenas' });
            fields.push({ id: 'dsrt-seruti',  name: 'petugas_seruti' });
        }
        populateModalSelects('modal-dsrt', fields, item);
    }

    var deleteUrlMap = { dssls: '/data-dssls', dsrt: '/data-dsrt', lapangan: '/petugas-lapangan', entry: '/petugas-entry' };
    var selectAllMap = { dssls: '#selectAllDssls', dsrt: '#selectAllDsrt', lapangan: '#selectAllLapangan', entry: '#selectAllEntry' };

    function reloadTable(type) {
        if (dataTables[type]) { dataTables[type].ajax.reload(null, false); $(selectAllMap[type]).prop('checked', false); }
    }

    function deleteSelected(type) {
        var ids = [];
        $('.row-' + type + '-checkbox:checked').each(function() { ids.push($(this).val()); });
        if (!ids.length) { Swal.fire('Peringatan','Pilih minimal satu data','warning'); return; }
        Swal.fire({ title:'Hapus data terpilih?', text:'Anda akan menghapus '+ids.length+' data!', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#ef4444', confirmButtonText:'Ya, Hapus!'
        }).then(function(r) {
            if (r.isConfirmed) $.post(deleteUrlMap[type]+'/delete-bulk',{ids:ids},function(res){
                if(res.success){Swal.fire('Terhapus!',res.message,'success');reloadTable(type);}
            });
        });
    }

    function deleteAll(type) {
        Swal.fire({ title:'Reset semua data?', text:'Seluruh data akan dihapus permanen!', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#ef4444', confirmButtonText:'Ya, Reset Semua!'
        }).then(function(r) {
            if (r.isConfirmed) $.post(deleteUrlMap[type]+'/delete-all',function(res){
                if(res.success){Swal.fire('Terhapus!',res.message,'success');reloadTable(type);}
            });
        });
    }

    // ── Column Definitions ───────────────────────────────────────────────────────

    function lapanganColumns() {
        var cols = [];
        if (isSuperAdmin) cols.push({ data:null, orderable:false, searchable:false, className:'text-center', render:function(d,t,row){return renderCheckbox(row,'lapangan');} });
        cols.push(
            { data:'kode_petugas', render:function(d){return '<span class="font-bold text-bps-dark text-xs">'+escapeHtml(d)+'</span>';} },
            { data:'provinsi', render:renderPlainText },
            { data:'kabupaten', render:renderPlainText },
            { data:'nama_petugas', render:function(d){return '<span class="font-medium text-xs">'+escapeHtml(d)+'</span>';} },
            { data:'no_hp', render:renderPlainText },
            { data:'jabatan', render:renderPlainText },
            { data:'status', render:function(d){return '<span class="px-2 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold uppercase">'+escapeHtml(d)+'</span>';} }
        );
        return cols;
    }

    function entryColumns() {
        var cols = [];
        if (isSuperAdmin) cols.push({ data:null, orderable:false, searchable:false, className:'text-center', render:function(d,t,row){return renderCheckbox(row,'entry');} });
        cols.push(
            { data:'kode_petugas', render:function(d){return '<span class="font-bold text-bps-dark text-xs">'+escapeHtml(d)+'</span>';} },
            { data:'provinsi', render:renderPlainText },
            { data:'kabupaten', render:renderPlainText },
            { data:'nama_petugas', render:function(d){return '<span class="font-medium text-xs">'+escapeHtml(d)+'</span>';} },
            { data:'email', render:renderPlainText },
            { data:'no_hp', render:renderPlainText },
            { data:'status', render:function(d){return '<span class="px-2 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold uppercase">'+escapeHtml(d)+'</span>';} }
        );
        return cols;
    }

    function dsslsColumns() {
        var cols = [];

        // ── Checkbox & Action: tidak ada untuk adminipds ──
        if (!isAdminIpds) {
            cols.push({ data:null, orderable:false, searchable:false, className:'text-center', render:function(d,t,row){return renderCheckbox(row,'dssls');} });
            cols.push({ data:null, orderable:false, searchable:false, render:function(){return renderActionButton('dssls');} });
        }

        // ── Data utama ──
        cols.push(
            { data:'nama_kecamatan', render:function(d,t,row){
                return '<p class="font-bold text-bps-dark text-xs">'+escapeHtml(row.nama_kecamatan)+'</p>'
                    +'<p class="text-[10px] text-gray-500 uppercase">'+escapeHtml(row.nama_desa_kelurahan)+'</p>';
            }},
            { data:'kode_sls', render:function(d,t,row){
                return '<p class="font-bold text-bps-dark text-xs">'+escapeHtml(row.kode_sls)+'</p>'
                    +'<p class="text-[10px] text-gray-500 uppercase truncate max-w-[150px]">'+escapeHtml(row.nama_sls)+'</p>';
            }},
            { data:'jumlah_keluarga_awal',              render:function(d,t,row){return renderIntCell(row,'jumlah_keluarga_awal','dssls',false);} },
            { data:'jumlah_keluarga_hasil_updating',    render:function(d,t,row){return renderIntCell(row,'jumlah_keluarga_hasil_updating','dssls',false);} },
            { data:'jumlah_rumah_tangga_hasil_updating',render:function(d,t,row){return renderIntCell(row,'jumlah_rumah_tangga_hasil_updating','dssls',false);} }
        );

        // ── Ceklis Lapangan ──
        // adminipds : disabled icon
        // semua lain : interactive
        cols.push({ data:'ceklis_lap', className:'text-center', render:function(d,t,row){
            return isAdminIpds ? renderCeklisIcon(row,'ceklis_lap') : renderStatusCheckbox(row,'dssls','ceklis_lap');
        }});

        // ── Ceklis Sosial ──
        cols.push({ data:'ceklis_sosial', className:'text-center', render:function(d,t,row){
            return isAdminIpds ? renderCeklisIcon(row,'ceklis_sosial') : renderStatusCheckbox(row,'dssls','ceklis_sosial');
        }});

        // ── Ceklis IPDS ──
        // adminipds   : interactive ✅
        // adminsosial : view-only icon
        // superadmin  : interactive ✅
        cols.push({ data:'ceklis_ipds', className:'text-center', render:function(d,t,row){
            if (isAdminSosial) return renderCeklisIcon(row,'ceklis_ipds');
            return renderStatusCheckbox(row,'dssls','ceklis_ipds');
        }});

        // ── Petugas PPL: superadmin & adminsosial ──
        if (isSuperAdmin || isAdminSosial) {
            cols.push({ data:'petugas_ppl_nama', render:function(d,t,row){return renderPetugasCell(row,'petugas_ppl','dssls');} });
        }

        // ── Petugas PML: superadmin & adminsosial ──
        if (isSuperAdmin || isAdminSosial) {
            cols.push({ data:'petugas_pml_nama', render:function(d,t,row){return renderPetugasCell(row,'petugas_pml','dssls');} });
        }

        // ── Petugas Entry: superadmin & adminipds ──
        if (isSuperAdmin || isAdminIpds) {
            cols.push({ data:'petugas_entry_nama', render:function(d,t,row){return renderPetugasCell(row,'petugas_entry','dssls');} });
        }

        return cols;
    }

    function dsrtColumns() {
        var cols = [];
        // isAdminIpds r203 dll read-only
        var ipdsReadOnly = isAdminIpds;

        // ── Checkbox: hanya superadmin ──
        if (isSuperAdmin) {
            cols.push({ data:null, orderable:false, searchable:false, className:'text-center', render:function(d,t,row){return renderCheckbox(row,'dsrt');} });
        }

        // ── Action button (semua role) ──
        cols.push({ data:null, orderable:false, searchable:false, render:function(){return renderActionButton('dsrt');} });

        // ── Data dasar (semua role) ──
        cols.push(
            { data:'kec', render:function(d,t,row){
                return '<p class="font-bold text-bps-dark text-xs">'+escapeHtml(row.nmkec||row.kec)+'</p>'
                    +'<p class="text-[10px] text-gray-500 uppercase">'+escapeHtml(row.nmdesa||row.desa)+'</p>';
            }},
            { data:'nmslsm', render:function(d,t,row){
                return '<p class="font-bold text-bps-dark text-xs truncate max-w-[100px]">'+escapeHtml(row.nmslsm)+'</p>'
                    +'<p class="text-[10px] text-gray-500 uppercase">'+escapeHtml(row.nks_sak22)+'</p>';
            }},
            { data:'r503', className:'font-medium text-xs', render:function(d){return escapeHtml(d);} }
        );

        // ── adminipds: ceklis IPDS di posisi PERTAMA di antara ceklis ──
        if (isAdminIpds) {
            cols.push({ data:'ceklis_ipds', className:'text-center', render:function(d,t,row){
                return renderStatusCheckbox(row,'dsrt','ceklis_ipds');
            }});
        }

        // ── Ceklis Lapangan ──
        cols.push({ data:'ceklis_lap', className:'text-center', render:function(d,t,row){
            return isAdminIpds ? renderCeklisIcon(row,'ceklis_lap') : renderStatusCheckbox(row,'dsrt','ceklis_lap');
        }});

        // ── Ceklis Sosial ──
        cols.push({ data:'ceklis_sosial', className:'text-center', render:function(d,t,row){
            return isAdminIpds ? renderCeklisIcon(row,'ceklis_sosial') : renderStatusCheckbox(row,'dsrt','ceklis_sosial');
        }});

        // ── Ceklis IPDS posisi normal: hanya superadmin (adminsosial: hidden, adminipds: sudah di atas) ──
        if (isSuperAdmin) {
            cols.push({ data:'ceklis_ipds', className:'text-center', render:function(d,t,row){
                return renderStatusCheckbox(row,'dsrt','ceklis_ipds');
            }});
        }

        // ── Ceklis Pemeriksaan ──
        cols.push({ data:'ceklis_pemeriksaan', className:'text-center', render:function(d,t,row){
            return isAdminIpds ? renderCeklisIcon(row,'ceklis_pemeriksaan') : renderStatusCheckbox(row,'dsrt','ceklis_pemeriksaan');
        }});

        // ── Petugas PPL & PML: superadmin & adminsosial ──
        if (isSuperAdmin || isAdminSosial) {
            cols.push({ data:'petugas_ppl_nama', render:function(d,t,row){return renderPetugasCell(row,'petugas_ppl','dsrt');} });
            cols.push({ data:'petugas_pml_nama', render:function(d,t,row){return renderPetugasCell(row,'petugas_pml','dsrt');} });
        }

        // ── Petugas Susenas & Seruti: superadmin & adminipds ──
        if (isSuperAdmin || isAdminIpds) {
            cols.push({ data:'petugas_susenas_nama', render:function(d,t,row){return renderPetugasCell(row,'petugas_susenas','dsrt');} });
            cols.push({ data:'petugas_seruti_nama',  render:function(d,t,row){return renderPetugasCell(row,'petugas_seruti','dsrt');} });
        }

        // ── R203 s/d Catatan KP: superadmin & adminsosial ──
        if (isSuperAdmin || isAdminSosial) {
            cols.push(
                { data:'r203_kor',         render:function(d,t,row){return renderEnumCell(row,'r203_kor',ipdsReadOnly);} },
                { data:'r203_kp',          render:function(d,t,row){return renderEnumCell(row,'r203_kp',ipdsReadOnly);} },
                { data:'r301_jumlah_art',  render:function(d,t,row){return renderIntCell(row,'r301_jumlah_art','dsrt',ipdsReadOnly);} },
                { data:'r304_vsen26kp',    render:function(d,t,row){return renderIntCell(row,'r304_vsen26kp','dsrt',ipdsReadOnly);} },
                { data:'r305_vsen26kp',    render:function(d,t,row){return renderIntCell(row,'r305_vsen26kp','dsrt',ipdsReadOnly);} },
                { data:'blok_catatan_kor', render:function(d,t,row){return renderBoolCell(row,'blok_catatan_kor',ipdsReadOnly);} },
                { data:'blok_catatan_kp',  render:function(d,t,row){return renderBoolCell(row,'blok_catatan_kp',ipdsReadOnly);} }
            );
        }

        return cols;
    }

    function columnsFor(section) {
        return { lapangan:lapanganColumns, entry:entryColumns, dssls:dsslsColumns, dsrt:dsrtColumns }[section]();
    }

    // ── DataTables Init ──────────────────────────────────────────────────────────

    $(document).ready(function() {

        var processingHtml = '<div class="custom-loader flex items-center justify-center space-x-1.5">'
            + '<div class="w-2.5 h-2.5 bg-[#FF8C00] rounded-full animate-bounce" style="animation-delay:-0.3s"></div>'
            + '<div class="w-2.5 h-2.5 bg-[#FF8C00] rounded-full animate-bounce" style="animation-delay:-0.15s"></div>'
            + '<div class="w-2.5 h-2.5 bg-[#FF8C00] rounded-full animate-bounce"></div>'
            + '</div>';

        var dtConfigs = {
            responsive:false, scrollX:false, autoWidth:false, searchDelay:350,
            language: {
                search:'', searchPlaceholder:'Cari data...', lengthMenu:'_MENU_ entries',
                processing:processingHtml,
                paginate:{ previous:'<i class="fa-solid fa-chevron-left"></i>', next:'<i class="fa-solid fa-chevron-right"></i>' }
            },
            drawCallback:function(){ $('.dataTables_paginate .paginate_button').addClass('rounded-xl border-0'); }
        };

        var serverEndpoints = {
            lapangan: '{{ route("dashboard.datatable_lapangan") }}',
            entry:    '{{ route("dashboard.datatable_entry") }}',
            dssls:    '{{ route("dashboard.datatable_dssls") }}',
            dsrt:     '{{ route("dashboard.datatable_dsrt") }}'
        };

        function initServerTable(section) {
            if (dataTables[section]) { dataTables[section].columns.adjust(); return; }
            $('#tbody-' + section).empty();
            dataTables[section] = $('#dt-' + section).DataTable($.extend(true,{},dtConfigs,{
                processing:true, serverSide:false, deferRender:true,
                ajax: serverEndpoints[section],
                order: [],          // Tidak ada initial sort — index kolom berbeda per role
                columns: columnsFor(section)
            }));
        }

        withPetugasOptions(); // pre-fetch

        var hash          = window.location.hash.substring(1);
        var activeSection = '{{ session("active_tab", "lapangan") }}';
        var available     = $('.sidebar-link').map(function(){ return $(this).data('section'); }).get();
        if (hash && available.includes(hash)) activeSection = hash;

        $('.sidebar-link').removeClass('active');
        var $activeLink = $('.sidebar-link[data-section="' + activeSection + '"]');
        $activeLink.addClass('active');
        $('#current-section-title').text($activeLink.find('span').text());
        $('.dashboard-section').addClass('hidden');
        $('#section-' + activeSection).removeClass('hidden');
        if (!window.location.hash) window.location.hash = activeSection;

        initServerTable(activeSection);
        $(document).on('tabChanged', function(e, section){ initServerTable(section); });

        // ── Inline: petugas dropdown ─────────────────────────────────────────────
        $(document).on('click', '.petugas-inline-trigger', function() {
            var $btn  = $(this), type = $btn.data('type'), field = $btn.data('field');
            var row   = dataTableRow($btn, type); if (!row) return;
            withPetugasOptions(function(options) {
                var rowData = row.data(), cfg = petugasConfig(field);
                var selected = rowData[field] || '';
                var selectClass = type === 'dssls' ? 'petugas-dssls-update' : 'petugas-dsrt-update';
                var html = '<select class="'+selectClass+' inline-petugas-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange"'
                    +' data-id="'+escapeHtml(rowData.id)+'" data-type="'+escapeHtml(type)+'" data-field="'+escapeHtml(field)+'" data-selected="'+escapeHtml(selected)+'">'
                    + buildOptionsHtml(options[cfg.key]||[], cfg.label) + '</select>';
                var $cell = $btn.closest('td'); $cell.html(html);
                var sel = $cell.find('select')[0]; sel.value = selected; sel.focus();
                try { sel.showPicker(); } catch(e) { sel.dispatchEvent(new MouseEvent('mousedown',{bubbles:true,cancelable:true,view:window})); }
            });
        });

        $(document).on('blur', '.inline-petugas-select', function() {
            var $s = $(this), type = $s.data('type'), field = $s.data('field');
            var row = dataTableRow($s, type);
            if (row) $s.closest('td').html(renderPetugasCell(row.data(), field, type));
        });

        // ── Inline: enum dropdown ────────────────────────────────────────────────
        $(document).on('click', '.enum-inline-trigger', function() {
            if (isAdminIpds) return;
            var $btn = $(this), field = $btn.data('field');
            var row  = dataTableRow($btn, 'dsrt'); if (!row) return;
            var rowData = row.data(), selected = rowData[field] || '';
            var opts = '<option value="">-- Pilih Status --</option>';
            r203StatusOptions.forEach(function(o){ opts += '<option value="'+o.kode+'">'+o.nama+'</option>'; });
            var html = '<select class="inline-enum-select text-[10px] font-bold bg-white/50 border border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange max-w-[150px]"'
                +' data-id="'+escapeHtml(rowData.id)+'" data-field="'+escapeHtml(field)+'">'+opts+'</select>';
            var $cell = $btn.closest('td'); $cell.html(html);
            var sel = $cell.find('select')[0]; sel.value = selected; sel.focus();
            try { sel.showPicker(); } catch(e) { sel.dispatchEvent(new MouseEvent('mousedown',{bubbles:true,cancelable:true,view:window})); }
        });

        $(document).on('change', '.inline-enum-select', function() {
            var $s = $(this), val = $s.val(), id = $s.data('id'), field = $s.data('field');
            var row = dataTableRow($s, 'dsrt');
            $s.css('opacity','0.5');
            $.post('/data-dsrt/update-inline',{id:id,field:field,value:val},function(res){
                if (!res.success){ Swal.fire('Error',res.message||'Gagal menyimpan data','error'); return; }
                if (row){ var rd=row.data(); rd[field]=val; row.data(rd); $s.closest('td').html(renderEnumCell(rd,field,false)); }
            }).fail(function(){ Swal.fire('Error','Koneksi error','error'); if(row) $s.closest('td').html(renderEnumCell(row.data(),field,false)); });
        });

        $(document).on('blur', '.inline-enum-select', function() {
            var $s = $(this), field = $s.data('field'), row = dataTableRow($s, 'dsrt');
            if (row) $s.closest('td').html(renderEnumCell(row.data(), field, false));
        });

        // ── Inline: bool dropdown ────────────────────────────────────────────────
        $(document).on('click', '.bool-inline-trigger', function() {
            if (isAdminIpds) return;
            var $btn = $(this), field = $btn.data('field');
            var row  = dataTableRow($btn, 'dsrt'); if (!row) return;
            var rowData = row.data(), selected = rowData[field];
            var val = (selected === null || selected === undefined) ? '' : (selected ? '1' : '0');
            var html = '<select class="inline-bool-select text-[10px] font-bold bg-white/50 border border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange max-w-[100px]"'
                +' data-id="'+escapeHtml(rowData.id)+'" data-field="'+escapeHtml(field)+'">'
                +'<option value="">-- Pilih --</option><option value="1">YA</option><option value="0">TIDAK</option></select>';
            var $cell = $btn.closest('td'); $cell.html(html);
            var sel = $cell.find('select')[0]; sel.value = val; sel.focus();
            try { sel.showPicker(); } catch(e) { sel.dispatchEvent(new MouseEvent('mousedown',{bubbles:true,cancelable:true,view:window})); }
        });

        $(document).on('change', '.inline-bool-select', function() {
            var $s = $(this), val = $s.val(), id = $s.data('id'), field = $s.data('field');
            var row = dataTableRow($s, 'dsrt');
            $s.css('opacity','0.5');
            $.post('/data-dsrt/update-inline',{id:id,field:field,value:val},function(res){
                if (!res.success){ Swal.fire('Error',res.message||'Gagal menyimpan data','error'); return; }
                if (row){ var rd=row.data(); rd[field]= val===''?null:(val==='1'); row.data(rd); $s.closest('td').html(renderBoolCell(rd,field,false)); }
            }).fail(function(){ Swal.fire('Error','Koneksi error','error'); if(row) $s.closest('td').html(renderBoolCell(row.data(),field,false)); });
        });

        $(document).on('blur', '.inline-bool-select', function() {
            var $s = $(this), field = $s.data('field'), row = dataTableRow($s, 'dsrt');
            if (row) $s.closest('td').html(renderBoolCell(row.data(), field, false));
        });

        // ── Inline: int input ────────────────────────────────────────────────────
        $(document).on('click', '.int-inline-trigger', function() {
            var $div = $(this), type = $div.data('type') || 'dsrt';
            // adminipds tidak boleh edit int di dsrt
            if (isAdminIpds && type === 'dsrt') return;
            var field = $div.data('field'), id = $div.data('id'), value = $div.data('value');
            var html = '<input type="number" class="inline-int-input text-xs font-semibold text-bps-dark bg-white border border-gray-300 rounded-lg px-2 py-1 w-full max-w-[80px] focus:ring-1 focus:ring-bps-orange focus:border-bps-orange"'
                +' data-id="'+escapeHtml(id)+'" data-field="'+escapeHtml(field)+'" data-type="'+escapeHtml(type)+'" data-original-value="'+escapeHtml(value)+'" value="'+escapeHtml(value)+'">';
            var $cell = $div.closest('td'); $cell.html(html);
            var inp = $cell.find('input')[0]; inp.focus(); inp.select();
        });

        function saveIntField($input) {
            if ($input.data('saving')) return; $input.data('saving', true);
            var value = $input.val(), id = $input.data('id'), field = $input.data('field'), type = $input.data('type') || 'dsrt';
            var row   = dataTableRow($input, type);
            if (value === String($input.data('original-value'))) {
                if (row) $input.closest('td').html(renderIntCell(row.data(), field, type, false));
                return;
            }
            $input.css('opacity','0.5');
            $.post('/data-'+type+'/update-inline',{id:id,field:field,value:value},function(res){
                if (!res.success){ Swal.fire('Error',res.message||'Gagal menyimpan data','error'); if(row) $input.closest('td').html(renderIntCell(row.data(),field,type,false)); return; }
                if (row){ var rd=row.data(); rd[field]=value===''?null:value; row.data(rd); $input.closest('td').html(renderIntCell(rd,field,type,false)); }
            }).fail(function(){ Swal.fire('Error','Koneksi error','error'); if(row) $input.closest('td').html(renderIntCell(row.data(),field,type,false)); });
        }

        $(document).on('keypress', '.inline-int-input', function(e){ if(e.which===13){e.preventDefault();saveIntField($(this));} });
        $(document).on('blur', '.inline-int-input', function(){ saveIntField($(this)); });

        // ── Select all checkboxes ────────────────────────────────────────────────
        $(document).on('change','#selectAllLapangan',function(){ $('.row-lapangan-checkbox').prop('checked',$(this).is(':checked')); });
        $(document).on('change','#selectAllEntry',   function(){ $('.row-entry-checkbox').prop('checked',$(this).is(':checked')); });
        $(document).on('change','#selectAllDssls',   function(){ $('.row-dssls-checkbox').prop('checked',$(this).is(':checked')); });
        $(document).on('change','#selectAllDsrt',    function(){ $('.row-dsrt-checkbox').prop('checked',$(this).is(':checked')); });

        // ── Inline petugas update ────────────────────────────────────────────────
        $(document).on('change', '.petugas-dssls-update, .petugas-dsrt-update', function() {
            var $s = $(this), val = $s.val(), text = val ? $s.find('option:selected').text() : null;
            var id = $s.data('id'), field = $s.data('field');
            var type = $s.data('type') || ($s.hasClass('petugas-dssls-update') ? 'dssls' : 'dsrt');
            var row  = dataTableRow($s, type);
            $s.css('opacity','0.5');
            $.post('/data-'+type+'/update-inline',{id:id,field:field,value:val},function(res){
                if (!res.success){ Swal.fire('Error',res.message||'Gagal menyimpan petugas','error'); return; }
                if (row){ var rd=row.data(); rd[field]=val; rd[field+'_nama']=text; row.data(rd); $s.closest('td').html(renderPetugasCell(rd,field,type)); }
            }).fail(function(){ Swal.fire('Error','Terjadi kesalahan sistem','error'); }).always(function(){ $s.css('opacity','1'); });
        });

        // ── Ceklis toggle ────────────────────────────────────────────────────────
        $(document).on('change', '.ceklis-toggle', function() {
            var $cb = $(this), isChecked = $cb.is(':checked') ? 1 : 0;
            var id = $cb.data('id'), type = $cb.data('type'), field = $cb.data('field');
            var row = dataTableRow($cb, type);
            $cb.parent().css('opacity','0.5');
            $.post('/data-'+type+'/toggle-ceklis',{id:id,field:field,state:isChecked},function(res){
                if (!res.success){ Swal.fire('Error','Gagal update status ceklis','error'); return; }
                var timeLabelId = '#lbl-'+field.replace('ceklis_','')+'-'+type+'-'+id;
                var timestamp = res.timestamp || '';
                $(timeLabelId).text(timestamp);
                if (row){ var rd=row.data(); rd[field]=Boolean(isChecked); rd['waktu_'+field]=timestamp; row.data(rd); }
            }).fail(function(){ Swal.fire('Error','Terjadi kesalahan sistem','error'); }).always(function(){ $cb.parent().css('opacity','1'); });
        });
    });
</script>
@endpush
