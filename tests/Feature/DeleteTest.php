<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PetugasLapangan;
use App\Models\PetugasEntry;
use App\Models\DataDssls;
use App\Models\DataDsrt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
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
     * Test that datatable responses include 'id' field for Petugas Lapangan & Petugas Entry.
     */
    public function test_datatables_include_id_field(): void
    {
        $pl = PetugasLapangan::create([
            'kode_petugas' => '1610001',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Petugas Lap 1',
            'no_hp'        => '08123456789',
            'kode_jabatan' => 1,
            'jabatan'      => 'Pencacah (PPL)',
            'status'       => 'Mitra',
        ]);

        $pe = PetugasEntry::create([
            'kode_petugas' => '1610002',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Petugas Entry 1',
            'email'        => 'entry1@bps.go.id',
            'no_hp'        => '08123456780',
            'status'       => 'Mitra',
        ]);

        $resLap = $this->actingAs($this->superadmin)
            ->getJson('/dashboard/datatable/lapangan');
        $resLap->assertStatus(200);
        $this->assertEquals($pl->kode_petugas, $resLap->json('data.0.kode_petugas'));

        $resEntry = $this->actingAs($this->superadmin)
            ->getJson('/dashboard/datatable/entry');
        $resEntry->assertStatus(200);
        $this->assertEquals($pe->kode_petugas, $resEntry->json('data.0.kode_petugas'));
    }

    /**
     * Test simultaneous bulk delete and delete all for Petugas Lapangan.
     */
    public function test_petugas_lapangan_delete_bulk_and_all(): void
    {
        $pl1 = PetugasLapangan::create([
            'kode_petugas' => '1610001',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Petugas Lap 1',
            'no_hp'        => '08123456789',
            'kode_jabatan' => 1,
            'jabatan'      => 'Pencacah (PPL)',
            'status'       => 'Mitra',
        ]);

        $pl2 = PetugasLapangan::create([
            'kode_petugas' => '1610002',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Petugas Lap 2',
            'no_hp'        => '08123456788',
            'kode_jabatan' => 2,
            'jabatan'      => 'Pengawas (PML)',
            'status'       => 'Mitra',
        ]);

        $pl3 = PetugasLapangan::create([
            'kode_petugas' => '1610003',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Petugas Lap 3',
            'no_hp'        => '08123456787',
            'kode_jabatan' => 1,
            'jabatan'      => 'Pencacah (PPL)',
            'status'       => 'Mitra',
        ]);

        // Bulk delete 2 items simultaneously
        $response = $this->actingAs($this->superadmin)
            ->postJson('/petugas-lapangan/delete-bulk', [
                'ids' => [$pl1->kode_petugas, $pl2->kode_petugas]
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('petugas_lapangans', ['kode_petugas' => $pl1->kode_petugas]);
        $this->assertDatabaseMissing('petugas_lapangans', ['kode_petugas' => $pl2->kode_petugas]);
        $this->assertDatabaseHas('petugas_lapangans', ['kode_petugas' => $pl3->kode_petugas]);

        // Delete all (reset)
        $responseAll = $this->actingAs($this->superadmin)
            ->postJson('/petugas-lapangan/delete-all');
        $responseAll->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseCount('petugas_lapangans', 0);
    }

    /**
     * Test simultaneous bulk delete and delete all for Petugas Entry.
     */
    public function test_petugas_entry_delete_bulk_and_all(): void
    {
        $pe1 = PetugasEntry::create([
            'kode_petugas' => '1610001',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Petugas Entry 1',
            'email'        => 'e1@bps.go.id',
            'no_hp'        => '08123456789',
            'status'       => 'Mitra',
        ]);

        $pe2 = PetugasEntry::create([
            'kode_petugas' => '1610002',
            'provinsi'     => 16,
            'kabupaten'    => 10,
            'nama_petugas' => 'Petugas Entry 2',
            'email'        => 'e2@bps.go.id',
            'no_hp'        => '08123456788',
            'status'       => 'Mitra',
        ]);

        // Bulk delete items simultaneously
        $response = $this->actingAs($this->superadmin)
            ->postJson('/petugas-entry/delete-bulk', [
                'ids' => [$pe1->kode_petugas, $pe2->kode_petugas]
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('petugas_entries', ['kode_petugas' => $pe1->kode_petugas]);
        $this->assertDatabaseMissing('petugas_entries', ['kode_petugas' => $pe2->kode_petugas]);
    }

    /**
     * Test simultaneous bulk delete and delete all for Data DSSLS.
     */
    public function test_data_dssls_delete_bulk_and_all(): void
    {
        $dssls1 = DataDssls::create([
            'provinsi' => 16, 'nama_provinsi' => 'SUMSEL', 'kabupaten' => 10, 'nama_kabupaten' => 'OGAN ILIR',
            'kecamatan' => 10, 'nama_kecamatan' => 'INDRALAYA', 'desa_kelurahan' => 1, 'nama_desa_kelurahan' => 'INDRALAYA',
            'klasifikasi_desa(k/p)' => 'K', 'strata_konsentrasi_kesejahteraan' => '1',
            'kode_sls' => '16100100010001', 'kode_sub_sls' => 0, 'nama_sls' => 'RT 01', 'nks' => 1001,
            'perkiraan_jumlah_keluarga' => 10,
        ]);

        $dssls2 = DataDssls::create([
            'provinsi' => 16, 'nama_provinsi' => 'SUMSEL', 'kabupaten' => 10, 'nama_kabupaten' => 'OGAN ILIR',
            'kecamatan' => 10, 'nama_kecamatan' => 'INDRALAYA', 'desa_kelurahan' => 1, 'nama_desa_kelurahan' => 'INDRALAYA',
            'klasifikasi_desa(k/p)' => 'K', 'strata_konsentrasi_kesejahteraan' => '1',
            'kode_sls' => '16100100010002', 'kode_sub_sls' => 0, 'nama_sls' => 'RT 02', 'nks' => 1002,
            'perkiraan_jumlah_keluarga' => 12,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->postJson('/data-dssls/delete-bulk', [
                'ids' => [$dssls1->id, $dssls2->id]
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('data_dssls', ['id' => $dssls1->id]);
        $this->assertDatabaseMissing('data_dssls', ['id' => $dssls2->id]);
    }

    /**
     * Test simultaneous bulk delete and delete all for Data DSRT.
     */
    public function test_data_dsrt_delete_bulk_and_all(): void
    {
        $dsrt1 = DataDsrt::create([
            'kec' => 10, 'desa' => 1, 'kdbs' => 101, 'klas' => 1, 'idbs' => 1001, 'dsrt_ssn' => 1, 'nus_ssn' => 1,
            'nmkec' => 'INDRALAYA', 'nmdesa' => 'INDRALAYA', 'nks_sak22' => 1001, 'F_SERUTI' => 1, 'nmslsm' => 'RT 01', 'r503' => 'Keluarga 1',
        ]);

        $dsrt2 = DataDsrt::create([
            'kec' => 10, 'desa' => 1, 'kdbs' => 102, 'klas' => 1, 'idbs' => 1002, 'dsrt_ssn' => 2, 'nus_ssn' => 2,
            'nmkec' => 'INDRALAYA', 'nmdesa' => 'INDRALAYA', 'nks_sak22' => 1002, 'F_SERUTI' => 1, 'nmslsm' => 'RT 02', 'r503' => 'Keluarga 2',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->postJson('/data-dsrt/delete-bulk', [
                'ids' => [$dsrt1->id, $dsrt2->id]
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('data_dsrts', ['id' => $dsrt1->id]);
        $this->assertDatabaseMissing('data_dsrts', ['id' => $dsrt2->id]);
    }
}
