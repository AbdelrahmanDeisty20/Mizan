<?php

return [
    'required' => 'The :attribute field is required.',
    'string'   => 'The :attribute field must be a string.',
    'email'    => 'The :attribute field must be a valid email address.',
    'max'      => [
        'numeric' => 'The :attribute field must not be greater than :max.',
        'file'    => 'The :attribute field must not be greater than :max kilobytes.',
        'string'  => 'The :attribute field must not be greater than :max characters.',
        'array'   => 'The :attribute field must not have more than :max items.',
    ],
    'min'      => [
        'numeric' => 'The :attribute field must be at least :min.',
        'file'    => 'The :attribute field must be at least :min kilobytes.',
        'string'  => 'The :attribute field must be at least :min characters.',
        'array'   => 'The :attribute field must have at least :min items.',
    ],
    'integer'              => 'The :attribute field must be an integer.',
    'exists'               => 'The selected :attribute is invalid.',
    'unique'               => 'The :attribute has already been taken.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',

    'attributes' => [
        'login'                 => 'email, phone, or syndicate card ID',
        'name'                  => 'full name',
        'office_name'           => 'office name',
        'degree_id'             => 'degree',
        'governorate_id'        => 'governorate',
        'office_address'        => 'office address',
        'email'                 => 'email address',
        'password'              => 'password',
        'password_confirmation' => 'password confirmation',
        'phone'                 => 'phone number',
        'syndicate_card_id'     => 'syndicate card ID',
        'office_phone'          => 'office phone',
        'role'                  => 'role',
        'code'                  => 'verification code',
        'token'                 => 'reset token',
        'national_id'           => 'national ID',
        'whatsapp'              => 'WhatsApp number',
        'address'               => 'address',
        'notes'                 => 'notes',
        'access_code'           => 'access code',
        'case_number'           => 'case number',
        'year'                  => 'year',
        'court_name'            => 'court name',
        'degree'                => 'degree',
        'case_type'             => 'case type',
        'status'                => 'status',
        'opponent_name'         => 'opponent name',
        'opponent_lawyer'       => "opponent's lawyer",
        'total_fees'            => 'total fees',
        'paid_fees'             => 'paid fees',
        'client_id'             => 'client',
        'client_role'           => 'client role in case',
        'court_id'              => 'court',
        'avatar'               => 'personal image',
        'syndicate_card_image' => 'syndicate card image',
        'image'                => 'image',
        'type'                 => 'image type',
        'hearing_date'         => 'hearing date',
        'hearing_time'         => 'hearing time',
        'hearing_type'         => 'hearing type',
        'hearing_type_id'      => 'hearing type',
        'hearing_type_name'    => 'hearing type name',
        'court_room'           => 'court room',
        'roll_number'          => 'roll number',
        'decision'             => 'decision',
        'requirements'         => 'requirements',
        'assigned_lawyer_id'   => 'assigned lawyer',
        'legal_case_id'        => 'legal case',
        'receipt_number'       => 'receipt number',
        'amount'               => 'amount collected',
        'payment_method'       => 'payment method',
        'receipt_date'         => 'receipt date',
        'issuer'               => 'issuer',
    ],


    'custom' => [
        'login' => [
            'required_without_all' => 'Either email, phone number, or syndicate card ID is required to login.',
        ],
        'hearing_date' => [
            'required'    => 'The hearing date field is required.',
            'date'        => 'The hearing date must be a valid date.',
            'date_format' => 'The hearing date must be in YYYY-MM-DD format.',
        ],
        'hearing_time' => [
            'required'    => 'The hearing time field is required.',
            'date_format' => 'The hearing time must be in HH:MM format (e.g. 09:30).',
        ],
        'hearing_type' => [
            'required' => 'The hearing type field is required.',
        ],
        'hearing_type_id' => [
            'required' => 'The hearing type field is required.',
            'exists'   => 'The selected hearing type does not exist.',
        ],
        'hearing_type_name' => [
            'unique' => 'The hearing type name has already been taken.',
        ],
        'status' => [
            'in' => 'The selected status is invalid.',
        ],
        'legal_case_id' => [
            'required' => 'The legal case field is required.',
            'exists'   => 'The selected legal case does not exist.',
        ],
        'assigned_lawyer_id' => [
            'exists' => 'The selected assigned lawyer does not exist.',
        ],
        'user_id' => [
            'exists' => 'The selected issuer lawyer does not exist.',
        ],
        'receipt_number' => [
            'unique' => 'The receipt number has already been taken.',
        ],
        'amount' => [
            'required' => 'The amount collected field is required.',
            'min'      => 'The amount collected must be greater than zero.',
        ],
        'payment_method' => [
            'required' => 'The payment method field is required.',
        ],
        'receipt_date' => [
            'required' => 'The receipt date field is required.',
            'date'     => 'The receipt date must be a valid date.',
        ],
        'degree_id' => [
            'exists' => 'The selected degree is invalid.',
        ],
        'governorate_id' => [
            'exists' => 'The selected governorate is invalid.',
        ],
        'email' => [
            'unique' => 'The email address is already registered.',
            'exists' => 'The email address is not registered.',
        ],
        'password' => [
            'confirmed' => 'The password confirmation does not match.',
            'letters'   => 'The password must contain at least one letter.',
        ],
        'role' => [
            'in' => 'The selected role is invalid. It must be lawyer or assistant.',
        ],
        'syndicate_card_id' => [
            'required_without' => 'Either syndicate card ID or phone number is required.',
        ],
        'phone' => [
            'required'         => 'The phone number field is required.',
            'not_found'        => 'The phone number is not found.',
            'required_without' => 'Either phone number or syndicate card ID is required.',
            'unique'           => 'The phone number has already been taken.',
        ],
        'access_code' => [
            'required' => 'The access code field is required.',
        ],
        'password' => [
            'required' => 'The password field is required.',
        ],
        'client_id' => [
            'required' => 'The client field is required.',
            'exists'   => 'The selected client does not exist.',
        ],
        'client_role' => [
            'in' => 'Client role must be one of: plaintiff, defendant, appellant, appellee, petitioner, respondent, intervener.',
        ],
        'court_id' => [
            'exists' => 'The selected court does not exist.',
        ],
        'case_type' => [
            'in' => 'Case type must be one of: civil, criminal, family, administrative, commercial, labor.',
        ],
        'degree' => [
            'in' => 'Degree must be one of: primary, appeal, cassation.',
        ],
        'year' => [
            'min' => 'The year must not be less than 1900.',
            'max' => 'The year must not be greater than 2100.',
        ],
        'total_fees' => [
            'required' => 'The total fees field is required.',
            'min'      => 'Total fees must not be less than zero.',
        ],
        'paid_fees' => [
            'required' => 'The paid fees field is required.',
            'min'      => 'Paid fees must not be less than zero.',
        ],
        'opponent_name' => [
            'required' => 'The opponent name field is required.',
        ],
        'notes' => [
            'required' => 'The case subject and requests field is required.',
        ],
        'status' => [
            'in'       => 'Case status must be one of: active, archived, closed, won, lost.',
            'required' => 'The case status field is required.',
        ],
        'trial_ends_at' => [
            'date' => 'The trial end date must be a valid date.',
        ],
    ],
];
