<?php

namespace Database\Seeders;

use App\Models\OurTeam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Doctor qualification updates from "Doctors Qualifications Oct 2026".
 * Sets education = qualifications and designation = <p>specialities</p> for a
 * consistent two-line display on the Our Team + speciality pages, and removes
 * Dr. Reanne Carvalho completely (detaches speciality links + soft-deletes).
 *
 * Idempotent: matches by name (case-insensitive) and only touches rows found.
 */
class DoctorQualificationsOct2026Seeder extends Seeder
{
    public function run(): void
    {
        // name => [qualifications (education), specialities (designation)]
        $data = [
            'Dr. Aishwarya Ramesh'      => ['MVSc, PG Dip (Small Animal Ultrasound), PG Dip (Feline Medicine, University of Sydney)', 'Emergency Critical Care, Anaesthesia'],
            'Dr. Abhilash Jadhao'       => ['MVSc, Ph.D. (Pathology)', 'Pathology, Cytopathology, Histopathology'],
            'Dr. Riddhi Sonigra'        => ['MVSc (Clinical Medicine)', 'Emergency Critical Care, Internal Medicine'],
            'Dr. Mukesh Kini'           => ['MVSc (Preventive Medicine & Epidemiology), PG Dip (One Health), PG Dip (Therapeutic Management of Infectious Diseases)', 'Soft Tissue Surgery, Anaesthesia, Emergency & Critical Care'],
            'Dr. Nalmas Dilip Kumar'    => ['MVSc (Surgery & Radiology)', 'Soft Tissue Surgery, Dentistry, Emergency & Critical Care'],
            'Dr. Neelam Singh'          => ['MVSc (Preventive Medicine)', 'Internal Medicine, Infectious & Vector-Borne Diseases'],
            'Dr. Rohit Prajapati'       => ['BVSc & AH, MRCVS (UK)', 'Emergency Critical Care'],
            'Dr. Roshni Tamhane Shah'   => ['BVSc & AH, MRCVS (UK), Cert in Advanced Veterinary Practice (SAC)', 'Cardiology, Anaesthesia, Dentistry'],
            'Dr. Sabhanpreet Singh'     => ['BVSc & AH', 'Emergency Critical Care'],
            'Dr. Abhisaar Kumar'        => ['BVSc & AH, PG Dip (Small Animal Clinical Practice)', 'Emergency Critical Care, Radiology'],
            'Dr. Akruti Pachpinde'      => ['MVSc', 'Emergency Critical Care'],
            'Dr. Ankush Gera'           => ['BVSc & AH', 'Emergency Critical Care'],
            'Dr. Hamid Shah'            => ['MVSc, Ph.D. (Microbiology)', 'Internal Medicine, Neurology, Imaging (USG, ECHO)'],
            'Dr. Harshada Game'         => ['MVSc', 'Clinical Medicine, Cardiology'],
            'Dr. Prutha Deshpande'      => ['MVSc (Internal Medicine)', 'Acupuncture, Critical Anaesthesia'],
            'Dr. Surbhi Gupta'          => ['MVSc, Ph.D. (Medicine)', 'Dermatology, Emergency Critical Care'],
            'Dr. Gautam Joglekar'       => ['MVSc', 'Advanced Imaging (USG, CT, MRI)'],

            // Not in the Oct 2026 doc — same content as before, but re-formatted from
            // the old <h4><strong> heading style into the consistent two-line layout.
            'Dr. Chiranjeovdhar Pathak' => ['B.V.Sc. & A.H., M.V.Sc. (Surgery), DIBVO', 'Veterinary Ophthalmic Surgeon | Diplomate, International Board of Veterinary Ophthalmology'],
            'Dr. Aakash Vedpathak'      => ['B.V.Sc. & A.H., M.V.Sc. (Surgery), DIBVO', 'Veterinary Ophthalmic Surgeon | Diplomate, International Board of Veterinary Ophthalmology'],
            'Dr. Vivek Thakur'          => ['B.V.Sc.', 'Veterinary Ophthalmology Resident'],
            'Dr. Noopur Desai'          => ['BVSc, MVSc (Surgery), Diplomate ACVR (Radiation Oncology) – USA', 'Senior Veterinary Specialist | Founder of The Cancer Vet'],
            'Dr. Namaeta Nadkarni'      => ['BVSc, MVSc (Surgery)', 'Veterinary Surgeon | Oncology & Advanced Surgery'],
            'Dr. Shreya Pandey'         => ['BVSc & A.H.', 'Veterinary Surgeon'],
            'Dr. Mukta Kubde'           => ['M.V.Sc.', 'Veterinary Oncology Resident'],
            'Dr. Shubhangi Yelmule'     => ['M.V.Sc.', 'Veterinary Oncology Resident'],
        ];

        foreach ($data as $name => [$education, $specialities]) {
            $member = OurTeam::whereNull('deleted_by')
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->first();

            if (! $member) {
                $this->command?->warn("Skipped (not found): {$name}");
                continue;
            }

            $member->update([
                'education'   => $education,
                // Wrapped in <p> so the view renders it as-is (not comma-split).
                'designation' => '<p>'.$specialities.'</p>',
                'updated_at'  => Carbon::now(),
            ]);
            $this->command?->info("Updated: {$name}");
        }

        // Remove Dr. Reanne Carvalho completely.
        $reanne = OurTeam::whereNull('deleted_by')
            ->whereRaw('LOWER(name) = ?', ['dr. reanne carvalho'])
            ->first();

        if ($reanne) {
            // Detach from any speciality so she disappears from speciality pages too.
            DB::table('speciality_detail_team')->where('our_team_id', $reanne->id)->delete();

            // App hides records by deleted_by (not just deleted_at), so set both.
            $reanne->update([
                'deleted_by' => 1, // Super Admin
                'deleted_at' => Carbon::now(),
            ]);
            $this->command?->info('Removed: Dr. Reanne Carvalho');
        } else {
            $this->command?->warn('Dr. Reanne Carvalho not found (already removed?).');
        }
    }
}
