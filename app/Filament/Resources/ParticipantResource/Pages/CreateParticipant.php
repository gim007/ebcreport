<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use App\Models\Participant;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateParticipant extends CreateRecord
{
    protected static string $resource = ParticipantResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'user_login_id' => $data['username'],
                'user_email'    => $data['stud_email'] ?? '',
                'user_password' => Hash::make($data['password']),
                'user_type'     => 'stud',
                'user_status'   => 'Active',
            ]);

            return Participant::create([
                'user_id'    => $user->user_id,
                'stud_fname' => $data['stud_fname'],
                'stud_lname' => $data['stud_lname'],
                'stud_email' => $data['stud_email'] ?? null,
                'tot_credit' => (int) ($data['tot_credit'] ?? 0),
            ]);
        });
    }
}
