<?php

namespace App\Actions;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterInstructorAction
{
    public function execute(array $data): Instructor
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'user_login_id' => $data['username'],
                'user_email'    => $data['email'],
                'user_password' => Hash::make($data['password']),
                'user_type'     => 'ins',
                'user_status'   => 'Active',
            ]);

            return Instructor::create([
                'user_id'        => $user->user_id,
                'uni_id'         => $data['org_id'],
                'ins_fname'      => $data['first_name'],
                'ins_lname'      => $data['last_name'],
                'ins_email'      => $data['email'],
                'admin_approval' => 'Pending',
                'is_hidden'      => false,
            ]);
        });
    }
}
