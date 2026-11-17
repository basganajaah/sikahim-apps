<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Notification;
use App\Models\Pendaftaran;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SikahimComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_admin_cannot_access_admin_panel(): void
    {
        $unverifiedAdmin = User::factory()->unverified()->create(['role' => 'admin']);

        $response = $this->actingAs($unverifiedAdmin)->get('/admin/home');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_dashboard_redirects_according_to_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('home'));
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('users-home'));
    }

    public function test_regular_user_cannot_view_unverified_or_rejected_activities(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $kategori = Kategori::create(['category_name' => 'Kajian']);

        $pending = Kegiatan::create([
            'id' => $admin->id,
            'category_id' => $kategori->category_id,
            'activity_name' => 'Kegiatan Pending Rahasia',
            'penyelenggara' => 'DKM',
            'datetime_started' => now()->addDays(2),
            'datetime_finished' => now()->addDays(2)->addHours(2),
            'location' => 'Masjid',
            'status' => 'Belum Mulai',
            'verification' => 'Menunggu verifikasi',
        ]);

        $this->actingAs($user)
            ->get(route('list-kegiatan'))
            ->assertOk()
            ->assertDontSee('Kegiatan Pending Rahasia');

        $this->actingAs($user)
            ->get(route('detail-kegiatan', $pending))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('evaluation', $pending))
            ->assertNotFound();
    }

    public function test_admin_can_create_edit_verify_reject_download_and_delete_activity(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $kategori = Kategori::create(['category_name' => 'Kajian']);
        $tag = Tag::create(['tag_name' => 'Islam']);

        $thumb = UploadedFile::fake()->image('poster.jpg');
        $materi1 = UploadedFile::fake()->create('materi1.pdf', 120, 'application/pdf');
        $materi2 = UploadedFile::fake()->create('materi2.pdf', 120, 'application/pdf');

        $this->actingAs($admin)->post(route('kegiatan.store'), [
            'thumbnail' => $thumb,
            'materi' => [$materi1, $materi2],
            'activity_name' => 'Kajian Rutin',
            'penyelenggara' => 'DKM SIKAHIM',
            'category_id' => $kategori->category_id,
            'sel2Category' => [$tag->tag_id],
            'datetime_started' => now()->addDays(3)->format('Y-m-d H:i'),
            'datetime_finished' => now()->addDays(3)->addHours(2)->format('Y-m-d H:i'),
            'max_participant' => 30,
            'location' => 'Aula Utama',
            'description' => 'Deskripsi kajian',
        ])->assertRedirect(route('list-kegiatan'));

        $kegiatan = Kegiatan::firstOrFail();
        $this->assertCount(2, $kegiatan->materi);

        // Remove first materi file during edit and change schedule to trigger schedule notification
        $removedPath = $kegiatan->materi[0];
        $keptPath = $kegiatan->materi[1];

        $this->actingAs($admin)->put(route('kegiatan.update', $kegiatan), [
            'activity_name' => 'Kajian Rutin Diperbarui',
            'penyelenggara' => 'DKM SIKAHIM',
            'category_id' => $kategori->category_id,
            'sel2Category' => [$tag->tag_id],
            'datetime_started' => now()->addDays(4)->format('Y-m-d H:i'),
            'datetime_finished' => now()->addDays(4)->addHours(2)->format('Y-m-d H:i'),
            'max_participant' => 40,
            'location' => 'Aula Baru',
            'description' => 'Deskripsi baru',
            'remove_materi' => [$removedPath],
        ])->assertRedirect(route('detail-kegiatan', $kegiatan));

        Storage::disk('public')->assertMissing($removedPath);
        Storage::disk('public')->assertExists($keptPath);
        $this->assertNotNull(Notification::where('title', 'Perubahan Jadwal: Kajian Rutin Diperbarui')->first());

        // Reject requires rejection_reason
        $this->actingAs($admin)->patch(route('kegiatan.update-status', $kegiatan), [
            'verification' => 'Ditolak',
            'status' => 'Ditunda',
            'rejection_reason' => 'Jadwal bentrok dengan kegiatan lain',
        ])->assertRedirect();

        $this->assertSame('Jadwal bentrok dengan kegiatan lain', $kegiatan->fresh()->rejection_reason);
        $this->assertNotNull(Notification::where('title', 'Kegiatan Ditolak: Kajian Rutin Diperbarui')->first());

        // Verify activity
        $this->actingAs($admin)->patch(route('kegiatan.update-status', $kegiatan), [
            'verification' => 'Terverifikasi',
            'status' => 'Belum Mulai',
        ])->assertRedirect();

        $this->assertNull($kegiatan->fresh()->rejection_reason);

        // Authenticated user can download materi
        $this->actingAs($user)
            ->get(route('kegiatan.materi.download', [$kegiatan, 0]))
            ->assertOk();

        // Notifications page & toggle read
        $this->actingAs($user)->get(route('notifications.index'))->assertOk();
        $firstNotif = Notification::firstOrFail();
        $this->actingAs($user)->patch(route('notifications.toggle', $firstNotif))->assertRedirect();

        // Delete activity cleans up remaining files
        $oldThumb = $kegiatan->fresh()->thumbnail;
        $this->actingAs($admin)->deleteJson(route('kegiatan.destroy', $kegiatan))->assertOk();
        Storage::disk('public')->assertMissing($oldThumb);
        Storage::disk('public')->assertMissing($keptPath);
    }

    public function test_registration_management_user_cancellation_and_strict_evaluation_rules(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $unregisteredUser = User::factory()->create(['role' => 'user']);
        $kategori = Kategori::create(['category_name' => 'Pelatihan']);

        $kegiatan = Kegiatan::create([
            'id' => $admin->id,
            'category_id' => $kategori->category_id,
            'activity_name' => 'Pelatihan Kepemimpinan',
            'penyelenggara' => 'DKM',
            'datetime_started' => now()->addDays(2),
            'datetime_finished' => now()->addDays(2)->addHours(2),
            'location' => 'Masjid',
            'max_participant' => 20,
            'status' => 'Belum Mulai',
            'verification' => 'Terverifikasi',
        ]);

        // User registers -> gets notification
        $this->actingAs($user)->post(route('user.pendaftaran.store', $kegiatan), [
            'registrant_name' => $user->name,
            'age' => 22,
            'gender' => true,
        ])->assertRedirect(route('user.pendaftaran'));

        $pendaftaran = Pendaftaran::firstOrFail();
        $this->assertNotNull(Notification::where('title', 'Pendaftaran Berhasil: Pelatihan Kepemimpinan')->first());

        // User cancels registration
        $this->actingAs($user)
            ->patch(route('user.pendaftaran.cancel', $pendaftaran))
            ->assertRedirect();
        $this->assertSame('Dibatalkan', $pendaftaran->fresh()->registration_status);

        // User can re-register after cancellation
        $this->actingAs($user)->post(route('user.pendaftaran.store', $kegiatan), [
            'registrant_name' => $user->name,
            'age' => 22,
            'gender' => true,
        ])->assertRedirect(route('user.pendaftaran'));

        // Admin approves registration
        $this->actingAs($admin)->patch(route('admin.pendaftaran.update-status', $pendaftaran), [
            'registration_status' => 'Diterima',
            'notes' => 'Peserta memenuhi syarat',
        ])->assertRedirect();
        $this->assertSame('Diterima', $pendaftaran->fresh()->registration_status);

        // Evaluation blocked while activity is not Selesai
        $this->actingAs($user)->post(route('evaluation.store', $kegiatan), [
            'rating' => 5,
            'review' => 'Bagus',
        ])->assertStatus(422);

        // Mark activity as Selesai
        $kegiatan->update(['status' => 'Selesai']);

        // Unregistered user cannot evaluate
        $this->actingAs($unregisteredUser)->post(route('evaluation.store', $kegiatan), [
            'rating' => 5,
            'review' => 'Bagus',
        ])->assertStatus(422);

        // Registered user can evaluate once
        $this->actingAs($user)->post(route('evaluation.store', $kegiatan), [
            'rating' => 5,
            'review' => 'Sangat bermanfaat',
        ])->assertRedirect(route('evaluation', $kegiatan));

        // Second evaluation by same user is rejected
        $this->actingAs($user)->post(route('evaluation.store', $kegiatan), [
            'rating' => 4,
            'review' => 'Evaluasi kedua',
        ])->assertStatus(422);
    }

    public function test_admin_user_management_kategori_tag_crud_reports_and_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        // Last admin cannot be demoted or deactivated
        $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
            'is_active' => true,
        ])->assertStatus(422);

        // Edit regular user, toggle active, and reset password
        $this->actingAs($admin)->get(route('users.show', $user))->assertOk();
        $this->actingAs($admin)->put(route('users.update', $user), [
            'name' => 'Jamaah Baru',
            'email' => 'jamaah_baru@example.com',
            'role' => 'user',
            'is_active' => true,
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('users.reset-password', $user), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));

        // Deactivate user and verify login is blocked
        $this->actingAs($admin)->patch(route('users.toggle-active', $user))->assertRedirect();
        $this->assertFalse($user->fresh()->is_active);

        $this->post('/logout');
        $this->post('/login', [
            'email' => 'jamaah_baru@example.com',
            'password' => 'newpassword123',
        ])->assertSessionHasErrors('email');

        // Kategori & Tag CRUD
        $this->actingAs($admin)->get(route('kategori-tag.index'))->assertOk();
        $this->actingAs($admin)->post(route('kategori.store'), ['category_name' => 'Sosial'])->assertRedirect();
        $kat = Kategori::firstOrFail();
        $this->actingAs($admin)->put(route('kategori.update', $kat), ['category_name' => 'Bakti Sosial'])->assertRedirect();
        $this->assertSame('Bakti Sosial', $kat->fresh()->category_name);

        $this->actingAs($admin)->post(route('tag.store'), ['tag_name' => 'Pemuda'])->assertRedirect();
        $tag = Tag::firstOrFail();
        $this->actingAs($admin)->put(route('tag.update', $tag), ['tag_name' => 'Remaja Masjid'])->assertRedirect();
        $this->assertSame('Remaja Masjid', $tag->fresh()->tag_name);

        // Search & filter on users-home
        $activeUser = User::factory()->create(['role' => 'user']);
        $this->actingAs($activeUser)->get(route('users-home', [
            'search' => 'Kajian',
            'category_id' => $kat->category_id,
            'tag_id' => $tag->tag_id,
            'location' => 'Masjid',
        ]))->assertOk();

        // Dashboard, CSV export & Audit logs
        $this->actingAs($admin)->get(route('home'))->assertOk();
        $this->actingAs($admin)->get(route('laporan.export-csv'))->assertOk();
        $this->actingAs($admin)->get(route('audit-logs.index'))->assertOk();
        $this->assertGreaterThan(0, AuditLog::count());

        $this->actingAs($admin)->delete(route('tag.destroy', $tag))->assertRedirect();
        $this->actingAs($admin)->delete(route('kategori.destroy', $kat))->assertRedirect();
    }
}
