<?php

return [
    'steps' => [
        [
            'Make yourself at home',
            'Create your account with your DELSU student details.',
        ],
        [
            'Get student verified',
            'Submit your student ID and current school-fee receipt.',
        ],
        [
            'Find it. Or list it.',
            'Explore campus finds or give your unused essentials a new home.',
        ],
        [
            'Pay with confidence',
            'Pay through Paystack. We verify payment before completion.',
        ],
        [
            'Make it official',
            'Confirm receipt when everything is right, or raise a dispute.',
        ],
    ],
    'security' => [
        [
            'shield',
            'Real student identities',
            'Student ID and fee receipts are reviewed privately by administrators.',
        ],
        [
            'lock',
            'An extra layer of protection',
            'Email OTP adds a second step to your account sign-in.',
        ],
        [
            'wallet',
            'Payment verified first',
            'Verified payments enter our protected transaction state before completion.',
        ],
        [
            'message',
            'Support when it matters',
            'Disputes pause normal release. Administrators review issues and fraud signals.',
        ],
    ],
    'faq' => [
        [
            'Who can buy and sell on DelsuMart?',
            'DELSU students with an active account and approved student verification can trade. Anyone can browse the public marketplace.',
        ],
        [
            'Why do I need to verify my identity?',
            'Verification helps keep our community accountable. Your student ID and fee receipt are stored privately and can only be accessed by you and authorized administrators.',
        ],
        [
            'How are payments protected?',
            'Payment is collected through Paystack and verified by DelsuMart. The transaction stays in our internal protected state until you confirm receipt or an administrator resolves a dispute. This is not native Paystack escrow.',
        ],
        [
            'What if something goes wrong?',
            'Open a dispute from your transaction before confirming receipt. Explain the issue and attach evidence. Normal transaction completion is paused during review.',
        ],
    ],
    'admin_stats' => [
        'students' => ['Total students', 'users'],
        'verified' => ['Verified students', 'shield'],
        'pending' => ['Pending KYC', 'clock'],
        'rejected' => ['Rejected KYC', 'file'],
        'listings' => ['Active listings', 'bag'],
        'held' => ['Protected transactions', 'wallet'],
        'disputes' => ['Open disputes', 'message'],
        'flags' => ['Open fraud flags', 'flag'],
    ],
    'student_stats' => [
        'active_listings' => ['Active listings', 'bag'],
        'purchases' => ['Purchases', 'wallet'],
        'sales' => ['Sales', 'bag'],
        'held' => ['Protected transactions', 'shield'],
    ],
    'transaction_states' => [
        'pending_payment' => 'Pending payment',
        'paid_held' => 'Paid / Protected',
        'release_pending' => 'Release pending',
        'released' => 'Released',
        'disputed' => 'Disputed',
        'refunded' => 'Refunded',
        'cancelled' => 'Cancelled',
    ],
    'sort_options' => [
        'latest' => 'Newest first',
        'price_asc' => 'Price: low to high',
        'price_desc' => 'Price: high to low',
        'oldest' => 'Oldest first',
    ],
];
