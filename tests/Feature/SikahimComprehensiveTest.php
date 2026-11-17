<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Notification;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_admin_can_create_edit_verify_and_delete_activity_with_file_cleanup(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $kategori = Kategori::create(['category_name' => 'Kajian']);
        $tag = Tag::create(['tag_name' => 'Islam']);

        $thumb = UploadedFile::fake()->image('poster.jpg');
        $materi = UploadedFile::fake()->create('materi.pdf', 120, 'application/pdf');

        $this->actingAs($admin)->post(route('kegiatan.store'), [
            'thumbnail' => $thumb,
            'materi' => [$materi],
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
        Storage::disk('public')->assertExists($kegiatan->thumbnail);
        Storage::disk('public')->assertExists($kegiatan->materi[0]);

        $this->actingAs($admin)->get(route('kegiatan.edit', $kegiatan))->assertOk();

        $this->actingAs($admin)->put(route('kegiatan.update', $kegiatan), [
            'activity_name' => 'Kajian Rutin Diperbarui',
            'penyelenggara' => 'DKM SIKAHIM',
            'category_id' => $kategori->category_id,
            'sel2Category' => [$tag->tag_id],
            'datetime_started' => now()->addDays(3)->format('Y-m-d H:i'),
            'datetime_finished' => now()->addDays(3)->addHours(2)->format('Y-m-d H:i'),
            'max_participant' => 40,
            'location' => 'Aula Utama',
            'description' => 'Deskripsi baru',
        ])->assertRedirect(route('detail-kegiatan', $kegiatan));

        $this->assertSame('Kajian Rutin Diperbarui', $kegiatan->fresh()->activity_name);

        $this->actingAs($admin)->patch(route('kegiatan.update-status', $kegiatan), [
            'verification' => 'Terverifikasi',
            'status' => 'Belum Mulai',
        ])->assertRedirect();

        $notif = Notification::where('title', 'Kegiatan Baru: Kajian Rutin Diperbarui')->first();
        $this->assertNotNull($notif);
        $this->assertStringContainsString('telah dibuka', $notif->message);

        // Mark read for admin should not mark read for regular user
        $this->actingAs($admin)->post(route('notifications.read'))->assertRedirect();
        $userNotifResponse = $this->actingAs($user)->getJson(route('notifications.get'))->assertOk();
        $this->assertGreaterThan(0, $userNotifResponse->json('label'));

        $oldThumb = $kegiatan->thumbnail;
        $oldMateri = $kegiatan->materi[0];
        $this->actingAs($admin)->deleteJson(route('kegiatan.destroy', $kegiatan))->assertOk();

        Storage::disk('public')->assertMissing($oldThumb);
        Storage::disk('public')->assertMissing($oldMateri);
    }

    public function test_postponed_or_completed_activity_blocks_registration_and_user_can_submit_evaluation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $kategori = Kategori::create(['category_name' => 'Pelatihan']);

        $ditunda = Kegiatan::create([
            'id' => $admin->id,
            'category_id' => $kategori->category_id,
            'activity_name' => 'Pelatihan Ditunda',
            'penyelenggara' => 'DKM',
            'datetime_started' => now()->addDays(3),
            'datetime_finished' => now()->addDays(3)->addHours(2),
            'location' => 'Masjid',
            'status' => 'Ditunda',
            'verification' => 'Terverifikasi',
        ]);

        $this->actingAs($user)->post(route('user.pendaftaran.store', $ditunda), [
            'registrant_name' => $user->name,
            'age' => 21,
            'gender' => true,
        ])->assertStatus(422);

        $this->actingAs($user)->post(route('evaluation.store', $ditunda), [
            'rating' => 5,
            'review' => 'Sangat bermanfaat',
        ])->assertRedirect(route('evaluation', $ditunda));

        $this->assertDatabaseHas('evaluasi', [
            'id' => $user->id,
            'activity_id' => $ditunda->activity_id,
            'rating' => 5,
            'review' => 'Sangat bermanfaat',
        ]);
    }

    public function test_admin_cannot_delete_another_admin_and_xss_is_escaped(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);
        $xssUser = User::factory()->create([
            'name' => '<script>alert(1)</script>',
            'role' => 'user',
        ]);

        $this->actingAs($admin1)
            ->deleteJson(route('users.destroy', $admin2))
            ->assertStatus(422);

        $json = $this->actingAs($admin1)
            ->getJson(route('users.index'))
            ->assertOk()
            ->json('data');

        $xssRow = collect($json)->firstWhere('id', $xssUser->id);
        $this->assertSame('&lt;script&gt;alert(1)&lt;/script&gt;', $xssRow['name']);
    }
}
