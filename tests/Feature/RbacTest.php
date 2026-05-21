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
            'kode_sls' => '32010100010001',
            'nama_sls' => 'RT 01',
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
            'kode_sls' => '32010100010001',
            'nama_sls' => 'RT 01',
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
}
