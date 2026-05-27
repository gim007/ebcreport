<?php

namespace App\Actions;

use App\Models\Course;
use App\Models\Participant;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterParticipantAction
{
    public function execute(array $data, Course $course, ?Scholarship $scholarship = null): Participant
    {
        return DB::transaction(function () use ($data, $course, $scholarship) {
            $user = User::create([
                'user_login_id' => $data['username'],
                'user_email'    => $data['email'],
                'user_password' => Hash::make($data['password']),
                'user_type'     => 'stud',
                'user_status'   => 'Active',
            ]);

            $credit = 0;
            if ($scholarship) {
                $scholarship->redeem();
                $credit = 1;
            }

            $participant = Participant::create([
                'user_id'      => $user->user_id,
                'stud_fname'   => $data['first_name'],
                'stud_lname'   => $data['last_name'],
                'stud_email'   => $data['email'],
                'stud_gender'  => $data['gender']  ?? null,
                'stud_phone'   => $data['phone']   ?? null,
                'stud_address' => $data['address'] ?? null,
                'stud_city'    => $data['city']    ?? null,
                'stud_state'   => isset($data['state'])   && $data['state']   !== '' ? strtoupper(trim($data['state']))   : null,
                'stud_zip'     => $data['zip']     ?? null,
                'stud_country' => isset($data['country']) && $data['country'] !== '' ? strtoupper(trim($data['country'])) : null,
                'tot_credit'   => $credit,
                'inst_id'      => $course->inst_id,
                'course_id'    => $course->course_id,
            ]);

            return $participant;
        });
    }
}
