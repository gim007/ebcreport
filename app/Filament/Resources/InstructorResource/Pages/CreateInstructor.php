<?php

namespace App\Filament\Resources\InstructorResource\Pages;

use App\Filament\Resources\InstructorResource;
use App\Models\Instructor;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateInstructor extends CreateRecord
{
    protected static string $resource = InstructorResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'user_login_id' => $data['username'],
                'user_email'    => $data['ins_email'] ?? '',
                'user_password' => Hash::make($data['password']),
                'user_type'     => 'ins',
                'user_status'   => 'Active',
            ]);

            return Instructor::create([
                'user_id'        => $user->user_id,
                'uni_id'         => $data['uni_id'],
                'ins_fname'      => $data['ins_fname'],
                'ins_lname'      => $data['ins_lname'],
                'ins_email'      => $data['ins_email'] ?? '',
                'admin_approval' => $data['admin_approval'] ?? 'Approved',
                'is_hidden'      => $data['is_hidden'] ?? false,
            ]);
        });
    }
}
