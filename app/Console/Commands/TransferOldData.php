<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Education;
use App\Models\Lesson;
use App\Models\Old\Cabang;
use App\Models\Old\Materi;
use App\Models\Old\Summary as OldSummary;
use App\Models\Old\User as OldUser;
use App\Models\Old\Wilayah;
use App\Models\Region;
use App\Models\Summary;
use App\Models\SummaryActiveStudentEducation;
use App\Models\SummaryActiveStudentLesson;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TransferOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transfer-old-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer Old Data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->setRegions();
        $this->setBranches();
        $this->setUsers();
        $this->setLesson();
        $this->setEducations();
        $this->setSummaries();
    }

    private function setRegions(): void
    {
        $wilayahs = Wilayah::all();

        $this->info('--- Region ---');
        $bar = $this->output->createProgressBar($wilayahs->count());

        $bar->start();

        foreach ($wilayahs as $wilayah) {
            Region::create([
                'name' => $wilayah->nama,
            ]);

            $bar->advance();
        }

        $bar->finish();
    }

    private function setBranches(): void
    {
        $cabangs = Cabang::with(['wilayah'])->get();

        $this->info('--- Branch ---');
        $bar = $this->output->createProgressBar($cabangs->count());

        $bar->start();

        foreach ($cabangs as $cabang) {
            Branch::create([
                'code' => $cabang->kode,
                'name' => $cabang->nama,
                'latitude' => $cabang->latitude,
                'longitude' => $cabang->longitude,
                'region_id' => Region::firstWhere('name', $cabang->wilayah->nama)->id ?? null,
            ]);

            $bar->advance();
        }

        $bar->finish();
    }

    private function setUsers()
    {
        $oldUsers = OldUser::all();

        $this->info('--- Users ---');
        $bar = $this->output->createProgressBar($oldUsers->count());

        $bar->start();

        foreach ($oldUsers as $oldUser) {
            $user = User::create([
                'name' => $oldUser->nama,
                'email' => $oldUser->email,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'status' => true,
            ]);

            switch ($oldUser->level_id) {
                case 1:
                    $user->assignRole('Admin');
                    break;

                case 2:
                    $user->assignRole('Owner');
                    break;

                case 3:
                    $user->assignRole('Approver');
                    break;

                default:
                    $user->assignRole('Submitter');
                    break;
            }

            $bar->advance();
        }

        $bar->finish();
    }

    private function setLesson()
    {
        $materis = [
            ['name' => 'DRUM'],
            ['name' => 'KEYBOARD'],
            ['name' => 'PIANO POP'],
            ['name' => 'PIANO KLASIK'],
            ['name' => 'VOCAL'],
            ['name' => 'BIOLA'],
            ['name' => 'GITAR KLASIK'],
            ['name' => 'GITAR BASS'],
            ['name' => 'GITAR ELEKTRIK'],
            ['name' => 'TEORI MUSIC'],
            ['name' => 'FLUTE'],
            ['name' => 'LITTLE MOZART'],
            ['name' => 'MUSIC DIGITAL'],
            ['name' => 'PERFORMANCE CLASS'],
            ['name' => 'SAXOPHONE'],
            ['name' => 'TEORI MUSIK'],
            ['name' => 'HOBY'],
            ['name' => 'VOKAL'],
            ['name' => 'UKULELE'],
            ['name' => 'GITAR--KLASIK'],
            ['name' => 'KELAS BAND'],
            ['name' => 'ENSAMBLE BIOLA'],
            ['name' => 'BIOLA WD'],
            ['name' => 'BIOLA WE'],
            ['name' => 'DRUM WD'],
            ['name' => 'DRUM WE'],
            ['name' => 'GITAR KLASIK WD'],
            ['name' => 'GITAR KLASIK WE'],
            ['name' => 'KEYBOARD WD'],
            ['name' => 'KEYBOARD WE'],
            ['name' => 'PIANO KLASIK HOBBY WD'],
            ['name' => 'PIANO KLASIK WD'],
            ['name' => 'PIANO KLASIK WE'],
            ['name' => 'PIANO POP WD'],
            ['name' => 'PIANO POP WE'],
            ['name' => 'VOKAL HOBBY WE'],
            ['name' => 'VOKAL WE'],
            ['name' => 'VOKALWD'],
        ];

        $this->info('--- Lesson ---');
        $bar = $this->output->createProgressBar(count($materis));

        $bar->start();

        foreach ($materis as $materi) {
            Lesson::create([
                'name' => $materi['name'],
            ]);

            $bar->advance();
        }

        $bar->finish();
    }

    private function setEducations()
    {
        $educations = [
            ['name' => 'TK'],
            ['name' => 'SD'],
            ['name' => 'SMP'],
            ['name' => 'SMA'],
            ['name' => 'UNIVERSITAS'],
            ['name' => 'UMUM'],
            ['name' => 'BIOLA'],
            ['name' => 'DRUM'],
            ['name' => 'GITAR BASS'],
            ['name' => 'GITAR ELEKTRIK'],
            ['name' => 'GITAR KLASIK'],
            ['name' => 'KEYBOARD'],
            ['name' => 'PIANO KLASIK'],
            ['name' => 'PIANO POP'],
            ['name' => 'TEORI MUSIC'],
            ['name' => 'VOCAL'],
        ];

        $this->info('--- Education ---');
        $bar = $this->output->createProgressBar(count($educations));

        $bar->start();

        foreach ($educations as $education) {
            Education::create([
                'name' => $education['name'],
            ]);

            $bar->advance();
        }

        $bar->finish();
    }

    private function setSummaries(): void
    {
        $summaries = OldSummary::with([
                'cabang',
                'user',
                'approver',
                'summaryASL.materi',
                'summaryASE.pendidikan',
            ])
            ->get();

        $this->info('--- Summary ---');
        $bar = $this->output->createProgressBar(count($summaries));

        $bar->start();

        foreach ($summaries as $summary) {
            $branch = Branch::firstWhere('name', $summary->cabang->nama);
            $user = User::firstWhere('email', $summary->user->email);
            $branch = Branch::firstWhere('name', $summary->cabang->nama);

            $newSummary = Summary::create([
                'month' => (int) $summary['bulan'],
                'year' => $summary['tahun'],
                'registration_fee' => $summary['uang_pendaftaran'],
                'course_fee' => $summary['uang_kursus'],
                'total_fee' => $summary['uang_pendaftaran'] + $summary['uang_kursus'],
                'royalty' => ($summary['uang_pendaftaran'] + $summary['uang_kursus']) * 0.1,
                'active_student' => $summary['siswa_aktif'],
                'new_student' => $summary['siswa_baru'],
                'inactive_student' => $summary['siswa_keluar'],
                'leave_student' => $summary['siswa_cuti'],
                'status' => true,
                'branch_id' => $branch->id,
                'user_id' => $user['id'],
                'approver_id' => $summary['approver_id'],
            ]);

            foreach ($summary->summaryASL as $summaryASL) {
                $namaMateri = Str::replace('# ', '', $summaryASL->materi->nama);
                $namaMateri = Str::replace('#', '', $namaMateri);

                $lesson = Lesson::firstWhere('name', $namaMateri);

                if (!$lesson) {
                    $lesson = Lesson::create([
                        'name' => $summaryASL->materi->nama,
                    ]);
                }

                SummaryActiveStudentLesson::create([
                    'total' => $summaryASL->jumlah,
                    'lesson_id' => $lesson->id,
                    'summary_id' => $newSummary->id,
                ]);
            }

            foreach ($summary->summaryASE as $summaryASE) {
                $education = Education::firstWhere('name', $summaryASE->pendidikan->nama);

                if (!$education) {
                    $education = Education::create([
                        'name' => $summaryASE->pendidikan->nama,
                    ]);
                }

                SummaryActiveStudentEducation::create([
                    'total' => $summaryASE->jumlah,
                    'education_id' => $education->id,
                    'summary_id' => $newSummary->id,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
