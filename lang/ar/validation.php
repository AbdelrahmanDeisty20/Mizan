<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'string'   => 'يجب أن يكون حقل :attribute نصاً.',
    'email'    => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صحيح.',
    'max'      => [
        'numeric' => 'يجب ألا تتجاوز قيمة :attribute :max.',
        'file'    => 'يجب ألا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'string'  => 'يجب ألا يتجاوز طول النص :attribute :max حرفاً.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر.',
    ],
    'min'      => [
        'numeric' => 'يجب أن تكون قيمة :attribute على الأقل :min.',
        'file'    => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'string'  => 'يجب أن يكون طول النص :attribute على الأقل :min حروف.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل :min عناصر.',
    ],
    'integer'              => 'يجب أن يكون حقل :attribute رقماً صحيحاً.',
    'exists'               => 'القيمة المحددة لـ :attribute غير موجودة.',
    'unique'               => 'قيمة :attribute مستخدمة بالفعل.',
    'required_without'     => 'حقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تتوفر باقي البيانات.',

    'attributes' => [
        'login'                 => 'البريد الإلكتروني أو الهاتف أو رقم الكارنيه',
        'name'                  => 'الاسم',
        'office_name'           => 'اسم المكتب',
        'degree_id'             => 'درجة القيد',
        'governorate_id'        => 'المحافظة',
        'office_address'        => 'عنوان المكتب',
        'email'                 => 'البريد الإلكتروني',
        'password'              => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'phone'                 => 'رقم الهاتف',
        'syndicate_card_id'     => 'رقم الكارنيه النقابي',
        'office_phone'          => 'هاتف المكتب',
        'role'                  => 'الدور الوظيفي',
        'code'                  => 'كود التحقق',
        'token'                 => 'رمز إعادة التعيين',
        'national_id'           => 'الرقم القومي',
        'whatsapp'              => 'واتساب',
        'address'               => 'العنوان',
        'notes'                 => 'الملاحظات',
        'access_code'           => 'كود الوصول',
        'case_number'           => 'رقم القضية',
        'year'                  => 'السنة',
        'court_name'            => 'اسم المحكمة',
        'degree'                => 'درجة التقاضي',
        'case_type'             => 'نوع القضية',
        'status'                => 'الحالة',
        'opponent_name'         => 'اسم الخصم',
        'opponent_lawyer'       => 'محامي الخصم',
        'total_fees'            => 'إجمالي الأتعاب',
        'paid_fees'             => 'المبلغ المدفوع',
        'client_id'             => 'الموكل',
        'client_role'           => 'صفة الموكل في الدعوى',
        'court_id'              => 'المحكمة',
        'avatar'               => 'الصورة الشخصية',
        'syndicate_card_image' => 'صورة كارنيه المحاماة',
        'image'                => 'الصورة',
        'type'                 => 'نوع الصورة',
    ],


    'custom' => [
        'login' => [
            'required_without_all' => 'يجب إدخال البريد الإلكتروني، أو رقم الهاتف، أو رقم الكارنيه النقابي لتسجيل الدخول.',
        ],
        'degree_id' => [
            'exists' => 'درجة القيد المحددة غير صالحة.',
        ],
        'governorate_id' => [
            'exists' => 'المحافظة المحددة غير صالحة.',
        ],
        'email' => [
            'unique' => 'البريد الإلكتروني مسجل بالفعل.',
            'exists' => 'البريد الإلكتروني غير مسجل لدينا.',
        ],
        'password' => [
            'confirmed' => 'تأكيد كلمة المرور غير متطابق مع كلمة المرور.',
            'letters'   => 'يجب أن تحتوي كلمة المرور على حرف واحد على الأقل.',
        ],
        'role' => [
            'in' => 'الدور الوظيفي المحدد غير صالح، يجب أن يكون محامي أو مساعد.',
        ],
        'syndicate_card_id' => [
            'required_without' => 'يجب إدخال رقم الكارنيه النقابي أو رقم الهاتف.',
        ],
        'phone' => [
            'required'         => 'رقم الهاتف مطلوب.',
            'not_found'        => 'رقم الهاتف غير صحيح.',
            'required_without' => 'يجب إدخال رقم الهاتف أو رقم الكارنيه النقابي.',
            'unique'           => 'رقم الهاتف مسجل بالفعل.',
        ],
        'access_code' => [
            'required' => 'حقل كود الوصول مطلوب.',
        ],
        'password' => [
            'required' => 'حقل كلمة المرور مطلوب.',
        ],
        'client_id' => [
            'required' => 'حقل الموكل مطلوب.',
            'exists'   => 'الموكل المحدد غير موجود.',
        ],
        'client_role' => [
            'in' => 'صفة الموكل يجب أن تكون: plaintiff, defendant, appellant, appellee, petitioner, respondent, intervener.',
        ],
        'court_id' => [
            'exists' => 'المحكمة المحددة غير موجودة.',
        ],
        'case_type' => [
            'in' => 'نوع الدعوى يجب أن يكون: civil, criminal, family, administrative, commercial, labor.',
        ],
        'degree' => [
            'in' => 'درجة التقاضي يجب أن تكون: primary, appeal, cassation.',
        ],
        'year' => [
            'min' => 'السنة يجب ألا تكون أقل من 1900.',
            'max' => 'السنة يجب ألا تتجاوز 2100.',
        ],
        'total_fees' => [
            'required' => 'حقل إجمالي الأتعاب مطلوب.',
            'min'      => 'إجمالي الأتعاب يجب ألا يكون أقل من صفر.',
        ],
        'paid_fees' => [
            'required' => 'حقل المبلغ المدفوع مطلوب.',
            'min'      => 'المبلغ المدفوع يجب ألا يكون أقل من صفر.',
        ],
        'opponent_name' => [
            'required' => 'حقل اسم الخصم مطلوب.',
        ],
        'notes' => [
            'required' => 'حقل موضوع وطلبات الدعوى مطلوب.',
        ],
        'status' => [
            'in'       => 'حالة القضية يجب أن تكون: active, archived, closed, won, lost.',
            'required' => 'حقل حالة القضية مطلوب.',
        ],
        'trial_ends_at' => [
            'date' => 'يجب أن يكون تاريخ نهاية المهلة صحيحًا.',
        ],
    ],
];
