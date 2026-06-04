<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DataDssls;
use App\Models\DataDsrt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $adminipds;
    private User $adminsosial;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'username' => 'superadmin',
            'role'     => 'superadmin',
        ]);

        $this->adminipds = User::factory()->create([
            'username' => 'adminipds',
            'role'     => 'adminipds',
        ]);

        $this->adminsosial = User::factory()->create([
            'username' => 'adminsosial',
            'role'     => 'adminsosial',
        ]);
    }

    /**
     * Test that adminipds can access import petugas entry but not petugas lapangan.
     */
    public function test_adminipds_import_permissions(): void
    {
        // Try importing petugas lapangan as adminipds -> should get 403 Forbidden
        $response = $this->actingAs($this->adminipds)
            ->post('/import-petugas-lapangan', []);
        $response->assertStatus(403);

        // Try importing petugas entry as adminipds -> should get 302 Redirect (since file is missing/empty, it redirects with validation error or similar, not 403)
        $response = $this->actingAs($this->adminipds)
            ->post('/import-petugas-entry', []);
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /**
     * Test that adminsosial can access import petugas lapangan but not petugas entry.
     */
    public function test_adminsosial_import_permissions(): void
    {
        // Try importing petugas entry as adminsosial -> should get 403 Forbidden
        $response = $this->actingAs($this->adminsosial)
            ->post('/import-petugas-entry', []);
        $response->assertStatus(403);

        // Try importing petugas lapangan as adminsosial -> should get 302 Redirect (not 403)
        $response = $this->actingAs($this->adminsosial)
            ->post('/import-petugas-lapangan', []);
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /**
     * Test that adminipds can only toggle ceklis_ipds in DSSLS.
     */
    public function test_adminipds_dssls_toggle_ceklis_permissions(): void
    {
        $dssls = DataDssls::create([
            'provinsi' => 32,
            'nama_provinsi' => 'JAWA BARAT',
            'kabupaten' => 1,
            'nama_kabupaten' => 'BOGOR',
            'kecamatan' => 10,
            'nama_kecamatan' => 'CIBINONG',
            'desa_kelurahan' => 1,
            'nama_desa_kelurahan' => 'CIBINONG',
            'klasifikasi_desa(k/p)' => 'K',
            'strata_konsentrasi_kesejahteraan' => '1',
            'kode_sls' => '32010100010001',
            'kode_sub_sls' => 0,
            'nama_sls' => 'RT 01',
            'nks' => 1234,
            'perkiraan_jumlah_keluarga' => 10,
        ]);

        // Allowed to toggle ceklis_ipds
        $response = $this->actingAs($this->adminipds)
            ->post('/data-dssls/toggle-ceklis', [
                'id'    => $dssls->id,
                'field' => 'ceklis_ipds',
                'state' => '1',
            ]);
        $response->assertStatus(200);

        // Forbidden to toggle ceklis_lap
        $response = $this->actingAs($this->adminipds)
            ->post('/data-dssls/toggle-ceklis', [
                'id'    => $dssls->id,
                'field' => 'ceklis_lap',
                'state' => '1',
            ]);
        $response->assertStatus(403);
    }

    /**
     * Test that adminsosial cannot toggle ceklis_ipds in DSSLS.
     */
    public function test_adminsosial_dssls_toggle_ceklis_permissions(): void
    {
        $dssls = DataDssls::create([
            'provinsi' => 32,
            'nama_provinsi' => 'JAWA BARAT',
            'kabupaten' => 1,
            'nama_kabupaten' => 'BOGOR',
            'kecamatan' => 10,
            'nama_kecamatan' => 'CIBINONG',
            'desa_kelurahan' => 1,
            'nama_desa_kelurahan' => 'CIBINONG',
            'klasifikasi_desa(k/p)' => 'K',
            'strata_konsentrasi_kesejahteraan' => '1',
            'kode_sls' => '32010100010001',
            'kode_sub_sls' => 0,
            'nama_sls' => 'RT 01',
            'nks' => 1234,
            'perkiraan_jumlah_keluarga' => 10,
        ]);

        // Forbidden to toggle ceklis_ipds
        $response = $this->actingAs($this->adminsosial)
            ->post('/data-dssls/toggle-ceklis', [
                'id'    => $dssls->id,
                'field' => 'ceklis_ipds',
                'state' => '1',
            ]);
        $response->assertStatus(403);

        // Allowed to toggle ceklis_lap
        $response = $this->actingAs($this->adminsosial)
            ->post('/data-dssls/toggle-ceklis', [
                'id'    => $dssls->id,
                'field' => 'ceklis_lap',
                'state' => '1',
            ]);
        $response->assertStatus(200);
    }

    /**
     * Test storing petugas lapangan and petugas entry.
     */
    public function test_store_petugas_lapangan_and_entry(): void
    {
        // 1. Storing petugas lapangan as superadmin should succeed and set default values
        $response = $this->actingAs($this->superadmin)
            ->postJson('/petugas-lapangan/store', [
                'kode_petugas' => '999999',
                'nama_petugas' => 'Test Lapangan',
                'no_hp'        => '081234567890',
                'jabatan'      => 'Pencacah (PPL)',
                'status'       => 'Mitra',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('petugas_lapangans', [
            'kode_petugas' => '999999',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Test Lapangan',
            'no_hp'        => '081234567890',
            'kode_jabatan' => 1,
            'jabatan'      => 'Pencacah (PPL)',
            'status'       => 'Mitra',
        ]);

        // 2. Try storing petugas lapangan as adminipds -> should get 403
        $response = $this->actingAs($this->adminipds)
            ->postJson('/petugas-lapangan/store', [
                'kode_petugas' => '888888',
                'nama_petugas' => 'Test Lapangan 2',
                'no_hp'        => '081234567891',
                'jabatan'      => 'Pengawas (PML)',
                'status'       => 'Staf Kabupaten',
            ]);
        $response->assertStatus(403);

        // 3. Storing petugas entry as adminipds should succeed
        $response = $this->actingAs($this->adminipds)
            ->postJson('/petugas-entry/store', [
                'kode_petugas' => '777777',
                'nama_petugas' => 'Test Entry',
                'email'        => 'testentry@bps.go.id',
                'no_hp'        => '081234567892',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('petugas_entries', [
            'kode_petugas' => '777777',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Test Entry',
            'email'        => 'testentry@bps.go.id',
            'no_hp'        => '081234567892',
            'status'       => 'Mitra',
        ]);

        // 4. Try storing petugas entry as adminsosial -> should get 403
        $response = $this->actingAs($this->adminsosial)
            ->postJson('/petugas-entry/store', [
                'kode_petugas' => '666666',
                'nama_petugas' => 'Test Entry 2',
                'email'        => 'testentry2@bps.go.id',
                'no_hp'        => '081234567893',
            ]);
        $response->assertStatus(403);
    }

    /**
     * Test export rekap petugas entry.
     */
    public function test_export_rekap_petugas(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get('/dashboard/export-rekap');

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition'), 'attachment; filename=rekap_penugasan_petugas_entry.xlsx')
        );
    }

    /**
     * Test that adminipds cannot update DSSLS integer fields (keluarga_awal, keluarga_hasil_updating, ruta_hasil_updating)
     * but adminsosial / superadmin can.
     */
    public function test_adminipds_dssls_inline_update_permissions(): void
    {
        $dssls = DataDssls::create([
            'provinsi' => 32,
            'nama_provinsi' => 'JAWA BARAT',
            'kabupaten' => 1,
            'nama_kabupaten' => 'BOGOR',
            'kecamatan' => 10,
            'nama_kecamatan' => 'CIBINONG',
            'desa_kelurahan' => 1,
            'nama_desa_kelurahan' => 'CIBINONG',
            'klasifikasi_desa(k/p)' => 'K',
            'strata_konsentrasi_kesejahteraan' => '1',
            'kode_sls' => '32010100010001',
            'kode_sub_sls' => 0,
            'nama_sls' => 'RT 01',
            'nks' => 1234,
            'perkiraan_jumlah_keluarga' => 10,
        ]);

        // adminipds tries to update jumlah_keluarga_awal inline -> 403 Forbidden
        $response = $this->actingAs($this->adminipds)
            ->postJson('/data-dssls/update-inline', [
                'id' => $dssls->id,
                'field' => 'jumlah_keluarga_awal',
                'value' => 12,
            ]);
        $response->assertStatus(403);

        // adminsosial tries to update jumlah_keluarga_awal inline -> 200 OK
        $response = $this->actingAs($this->adminsosial)
            ->postJson('/data-dssls/update-inline', [
                'id' => $dssls->id,
                'field' => 'jumlah_keluarga_awal',
                'value' => 12,
            ]);
        $response->assertStatus(200);
    }
}
