<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function search($data){
        $users = $this->userRepository->getUsers(
            ['id', 'first_name', 'last_name', 'mobile', 'email', 'address', 'created_at'],
            ['account_number', 'roles']
        )
            ->where(function($query) use ($data) {

                $query->when(!empty($input['term']), static function ($q) use ($input) {
                    $split = explode(' ', $input['term']);
                    // for first and last name search

                    if(isset($split[0], $split[1], $split[2])) {
                        $q->where('users.first_name', $split[0])
                            ->where('users.middle_name', $split[1])
                            ->where('users.last_name', $split[2]);

                    }elseif(isset($split[0], $split[1])) {
                        $q->where('users.first_name', $split[0])
                            ->where('users.last_name', $split[1])
                            ->orWhere('users.middle_name', $split[1]);

                    }else{
                        $q->where('users.first_name', 'like', '%' . $input['term'] . '%')
                            ->orWhere('users.last_name', 'like', '%' . $input['term'] . '%')
                            ->orWhere('users.email', 'like', '%' . $input['term'] . '%')
                            ->orWhere('users.country_iso', $input['term'])
                            ->orWhere('application_businesses.business_name', 'like', '%' . $input['term'] . '%');
                    }
                });

            })
            ->when(!empty($input['country_residence_id']), static function ($q) use($input) {

                // join country_residence and country_residence_iso to get country name
                // if cohort 1 is selected in the filter, use a different country table
                if(!empty($input['cohort']) && $input['cohort'] === 1) {
                    $q->where('users.country_id', $input['country_residence_id']);
                }else{
                    $q->where('users.country_residence_id', $input['country_residence_id'])
                        ->orWhere('country_residence_iso.id', $input['country_residence_id']);
                }
            })
            ->when(!empty($input['dob_start']), static function ($q) use($input) {
                $q->where('users.date_of_birth', '>=', date("Y-m-d", strtotime($input['dob_start'])));
            })
            ->when(!empty($input['dob_end']), static function ($q) use($input) {
                $q->where('users.date_of_birth', '<=', date("Y-m-d", strtotime($input['dob_end'])));
            })
            ->when(!empty($input['application_date_start']), static function ($q) use($input) {
                $q->where('users.created_at', '>=', date("Y-m-d", strtotime($input['application_date_start'])));
            })
            ->when(!empty($input['application_date_end']), static function ($q) use($input) {
                $q->where('users.created_at', '<=', date("Y-m-d", strtotime($input['application_date_end'])));
            })
            ->when(!empty($input['selected']), static function ($q) use($input) {
                if ($input['selected'] === 'selected') {
                    $q->where('users.selected', 1);
                }
                $q->where('users.selected', 0);
            })
            ->when(!empty($input['cohort']), static function ($q) use($input) {
                $q->where('users.cohort', $input['cohort']);
            })
            ->when(!empty($input['batch']), static function ($q) use($input) {
                $q->where('users.batch', $input['batch']);
            })
            ->when(!empty($input['gender']), static function ($q) use($input) {
                $q->where('users.gender', $input['gender']);
            })
            ->groupBy('users.id')
            ->paginate(15);

        // if result exists return results, else return empty array
        if($applications->total() > 0){
            return [
                'success' => true,
                'applications' => BraceApplicationSearchResource::collection($applications)->response()->getData(true),
                'total' => $applications->total(),
                'search_values' => Session::get('search_values')
            ];
        }

        return [
            'success' => true,
            'applications' => [],
            'total' => 0,
            'search_values' => Session::get('search_values')
        ];
    }
}
