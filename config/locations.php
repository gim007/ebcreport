<?php

/*
 * Static reference data for country + US state pickers.
 *
 * Shared by Filament resources (admin) and Blade views (public registration
 * / payment / account forms) so the lists stay identical everywhere.
 *
 * Access:
 *   config('locations.countries')   // ['US' => 'United States', ...]
 *   config('locations.us_states')   // ['AL' => 'Alabama', ...]
 *
 * Country codes are ISO-3166-1 alpha-2. US state codes are USPS 2-letter
 * abbreviations, matching legacy storage in `ebc_student.stud_state`.
 */

return [

    'countries' => [
        'US' => 'United States',
        'CA' => 'Canada',
        'GB' => 'United Kingdom',
        'AU' => 'Australia',
        'IN' => 'India',
        'DE' => 'Germany',
        'FR' => 'France',
        'IE' => 'Ireland',
        'NZ' => 'New Zealand',
        'ZA' => 'South Africa',
        'MX' => 'Mexico',
        'BR' => 'Brazil',
        'JP' => 'Japan',
        'SG' => 'Singapore',
        'NL' => 'Netherlands',
        'ES' => 'Spain',
    ],

    'us_states' => [
        'AL' => 'Alabama',        'AK' => 'Alaska',         'AZ' => 'Arizona',        'AR' => 'Arkansas',
        'CA' => 'California',     'CO' => 'Colorado',       'CT' => 'Connecticut',    'DE' => 'Delaware',
        'DC' => 'District of Columbia',
        'FL' => 'Florida',        'GA' => 'Georgia',        'HI' => 'Hawaii',         'ID' => 'Idaho',
        'IL' => 'Illinois',       'IN' => 'Indiana',        'IA' => 'Iowa',           'KS' => 'Kansas',
        'KY' => 'Kentucky',       'LA' => 'Louisiana',      'ME' => 'Maine',          'MD' => 'Maryland',
        'MA' => 'Massachusetts',  'MI' => 'Michigan',       'MN' => 'Minnesota',      'MS' => 'Mississippi',
        'MO' => 'Missouri',       'MT' => 'Montana',        'NE' => 'Nebraska',       'NV' => 'Nevada',
        'NH' => 'New Hampshire',  'NJ' => 'New Jersey',     'NM' => 'New Mexico',     'NY' => 'New York',
        'NC' => 'North Carolina', 'ND' => 'North Dakota',   'OH' => 'Ohio',           'OK' => 'Oklahoma',
        'OR' => 'Oregon',         'PA' => 'Pennsylvania',   'RI' => 'Rhode Island',   'SC' => 'South Carolina',
        'SD' => 'South Dakota',   'TN' => 'Tennessee',      'TX' => 'Texas',          'UT' => 'Utah',
        'VT' => 'Vermont',        'VA' => 'Virginia',       'WA' => 'Washington',     'WV' => 'West Virginia',
        'WI' => 'Wisconsin',      'WY' => 'Wyoming',
    ],

];
